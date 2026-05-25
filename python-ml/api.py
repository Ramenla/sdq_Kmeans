"""
=================================================================
Server API Python untuk Komputasi Machine Learning (K-Means)
=================================================================
Framework : Flask
Library ML : scikit-learn, pandas, numpy
Port       : 5000

Aturan Baku ML:
  - Data yang diproses HANYA 5 variabel subskala SDQ:
    E  (Emotional Problems)
    C  (Conduct Problems)
    H  (Hyperactivity)
    P  (Peer Problems)
    Pr (Prosocial Behaviour)
  - Variabel 'Diff' / 'skor_kesulitan' (Total Kesulitan)
    Bisa diikutsertakan secara dinamis jika user mencentang checkbox.
=================================================================
"""

from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sklearn.preprocessing import StandardScaler
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
from sklearn.metrics import silhouette_score

app = Flask(__name__)

# ---------------------------------------------------------------
# Kolom subskala SDQ yang digunakan untuk komputasi ML
# ---------------------------------------------------------------
FEATURE_COLUMNS = ['e_score', 'c_score', 'h_score', 'p_score', 'pro_score', 'skor_kesulitan']


def _validate_and_extract(payload):
    """
    Validasi payload JSON dari Laravel dan ekstrak DataFrame
    untuk variabel yang dikirim (bisa subset dari 5 variabel).

    Parameters
    ----------
    payload : dict
        JSON body dari request, harus memiliki key 'data' berisi
        list of dict (setiap dict = 1 baris data siswa).

    Returns
    -------
    tuple (pd.DataFrame, flask.Response or None)
        DataFrame jika valid, atau (None, error_response) jika tidak.
    """
    data = payload.get('data')

    if data is None or not isinstance(data, list) or len(data) == 0:
        return None, (jsonify({
            'status': 'error',
            'message': 'Payload "data" kosong atau tidak valid. '
                        'Kirim array of objects dengan key dari: '
                        + ', '.join(FEATURE_COLUMNS)
        }), 422)

    df = pd.DataFrame(data)

    # Deteksi kolom fitur yang ADA dalam data (bisa subset)
    # Laravel hanya mengirim kolom yang di-centang user
    available_cols = [col for col in FEATURE_COLUMNS if col in df.columns]

    if len(available_cols) == 0:
        return None, (jsonify({
            'status': 'error',
            'message': 'Tidak ada kolom variabel SDQ yang valid ditemukan dalam data. '
                        f'Kolom yang diharapkan: {FEATURE_COLUMNS}'
        }), 422)

    # Ambil hanya kolom yang tersedia
    df_features = df[available_cols].copy()

    # Konversi ke numerik, paksa error jadi NaN lalu isi 0

    for col in available_cols:
        df_features[col] = pd.to_numeric(df_features[col], errors='coerce').fillna(0)

    return df_features, None


# ===============================================================
# ENDPOINT 1: POST /api/preprocess
# ---------------------------------------------------------------
# Menerima JSON data mentah → StandardScaler (Z-Score)
# Mengembalikan data yang sudah distandarisasi.
# ===============================================================
@app.route('/api/preprocess', methods=['POST'])
def preprocess():
    payload = request.get_json(force=True)
    df_features, error = _validate_and_extract(payload)
    if error:
        return error

    # StandardScaler (Z-Score Normalization)
    scaler = StandardScaler()
    scaled_data = scaler.fit_transform(df_features)

    # Buat DataFrame hasil standarisasi dengan kolom yang sama
    used_columns = df_features.columns.tolist()
    df_scaled = pd.DataFrame(scaled_data, columns=used_columns)

    # Bulatkan ke 4 desimal agar mudah dibaca di tabel preview
    df_scaled = df_scaled.round(4)

    return jsonify({
        'status': 'success',
        'message': 'Preprocessing (Z-Score) berhasil.',
        'columns': used_columns,
        'scaled_data': df_scaled.to_dict(orient='records'),
        'scaler_mean': scaler.mean_.tolist(),
        'scaler_std': scaler.scale_.tolist(),
    }), 200


# ===============================================================
# ENDPOINT 2: POST /api/elbow
# ---------------------------------------------------------------
# Menerima JSON data mentah → StandardScaler → KMeans K=1..10
# Mengembalikan array Inertia (WCSS) untuk grafik Elbow.
# ===============================================================
@app.route('/api/elbow', methods=['POST'])
def elbow():
    payload = request.get_json(force=True)
    df_features, error = _validate_and_extract(payload)
    if error:
        return error

    # StandardScaler
    scaler = StandardScaler()
    scaled_data = scaler.fit_transform(df_features)

    # Hitung Inertia dan Silhouette untuk K=1 hingga K=10
    max_k = min(10, len(df_features))  # K tidak boleh melebihi jumlah data
    inertias = []
    silhouettes = []
    k_values_silhouette = []
    k_values = list(range(1, max_k + 1))

    for k in k_values:
        kmeans = KMeans(
            n_clusters=k,
            init='k-means++',
            n_init=10,
            max_iter=300,
            random_state=42
        )
        kmeans.fit(scaled_data)
        inertias.append(round(float(kmeans.inertia_), 4))

        # Silhouette score hanya bisa dihitung jika K >= 2 dan K < jumlah data
        if k >= 2 and k < len(df_features):
            score = silhouette_score(scaled_data, kmeans.labels_)
            silhouettes.append(round(float(score), 4))
            k_values_silhouette.append(k)

    return jsonify({
        'status': 'success',
        'message': f'Evaluasi Model (Elbow & Silhouette) berhasil dihitung untuk K=1 hingga K={max_k}.',
        'max_k': max_k,
        'k_values': k_values,
        'k_values_silhouette': k_values_silhouette,
        'inertia': inertias,
        'silhouette': silhouettes,
    }), 200


# ===============================================================
# ENDPOINT 3: POST /api/kmeans
# ---------------------------------------------------------------
# Menerima JSON data mentah DAN parameter 'jumlah_k'
# → StandardScaler → KMeans(n_clusters=jumlah_k)
# Mengembalikan label klaster untuk setiap baris data.
# ===============================================================
@app.route('/api/kmeans', methods=['POST'])
def kmeans_clustering():
    payload = request.get_json(force=True)

    # Validasi parameter jumlah_k
    jumlah_k = payload.get('jumlah_k')
    if jumlah_k is None:
        return jsonify({
            'status': 'error',
            'message': 'Parameter "jumlah_k" wajib diisi.'
        }), 422

    try:
        jumlah_k = int(jumlah_k)
    except (ValueError, TypeError):
        return jsonify({
            'status': 'error',
            'message': '"jumlah_k" harus berupa bilangan bulat.'
        }), 422

    if jumlah_k < 2:
        return jsonify({
            'status': 'error',
            'message': '"jumlah_k" minimal bernilai 2.'
        }), 422

    df_features, error = _validate_and_extract(payload)
    if error:
        return error

    if jumlah_k > len(df_features):
        return jsonify({
            'status': 'error',
            'message': f'"jumlah_k" ({jumlah_k}) tidak boleh melebihi '
                        f'jumlah data ({len(df_features)}).'
        }), 422

    # StandardScaler
    scaler = StandardScaler()
    scaled_data = scaler.fit_transform(df_features)

    # K-Means Clustering
    kmeans = KMeans(
        n_clusters=jumlah_k,
        init='k-means++',
        n_init=10,
        max_iter=300,
        random_state=42
    )
    kmeans.fit(scaled_data)

    # Label klaster untuk setiap baris (0-indexed)
    labels = kmeans.labels_.tolist()

    # Centroid tiap klaster (dalam skala Z-Score)
    centroids = kmeans.cluster_centers_.tolist()

    # PCA — Reduksi dimensi ke 2D untuk visualisasi Scatter Plot
    pca = PCA(n_components=2)
    pca_data = pca.fit_transform(scaled_data)
    pca_x = [round(float(x), 4) for x in pca_data[:, 0]]
    pca_y = [round(float(y), 4) for y in pca_data[:, 1]]

    # Profiling Klaster — Rata-rata skor SDQ mentah per klaster
    df_profiling = df_features.copy()
    df_profiling['cluster_label'] = labels
    cluster_profiles = df_profiling.groupby('cluster_label').mean().round(2)

    # Konversi ke list of dict agar mudah dikonsumsi JSON
    # Setiap elemen: { "cluster_label": 0, "e_score": 4.12, ... }
    profiles_list = []
    for cluster_label, row in cluster_profiles.iterrows():
        profile = {'cluster_label': int(cluster_label)}
        profile.update(row.to_dict())
        profiles_list.append(profile)

    return jsonify({
        'status': 'success',
        'message': f'K-Means Clustering berhasil dengan K={jumlah_k}.',
        'jumlah_k': jumlah_k,
        'labels': labels,
        'centroids': centroids,
        'inertia': round(float(kmeans.inertia_), 4),
        'n_iter': int(kmeans.n_iter_),
        'pca_x': pca_x,
        'pca_y': pca_y,
        'pca_explained_variance': [round(float(v), 4) for v in pca.explained_variance_ratio_],
        'cluster_profiles': profiles_list,
    })


# ===============================================================
# MAIN — Jalankan server di port 5000
# ===============================================================
if __name__ == '__main__':
    print("=" * 60)
    print(" SDQ K-Means ML API Server")
    print(" Running on http://127.0.0.1:5000")
    print("=" * 60)
    app.run(host='127.0.0.1', port=5000, debug=True)
