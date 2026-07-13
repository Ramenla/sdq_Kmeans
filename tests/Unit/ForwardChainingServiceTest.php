<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ForwardChainingService;

class ForwardChainingServiceTest extends TestCase
{
    protected ForwardChainingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Inisialisasi service yang akan diuji
        $this->service = new ForwardChainingService();
    }

    /**
     * Test logika Total Kesulitan SDQ (E+C+H+P)
     */
    public function test_classify_total_kesulitan_is_correct(): void
    {
        // Rule 1: Normal (0-15)
        $this->assertEquals('Normal', $this->service->classifyTotalKesulitan(10));
        $this->assertEquals('Normal', $this->service->classifyTotalKesulitan(15));
        
        // Rule 2: Borderline (16-19)
        $this->assertEquals('Borderline', $this->service->classifyTotalKesulitan(16));
        $this->assertEquals('Borderline', $this->service->classifyTotalKesulitan(19));
        
        // Rule 3: Abnormal (20-40)
        $this->assertEquals('Abnormal', $this->service->classifyTotalKesulitan(20));
        $this->assertEquals('Abnormal', $this->service->classifyTotalKesulitan(40));
    }

    /**
     * Test logika Emosional (E)
     */
    public function test_classify_emosional_is_correct(): void
    {
        $this->assertEquals('Normal', $this->service->classifyEmosional(5));
        $this->assertEquals('Borderline', $this->service->classifyEmosional(6));
        $this->assertEquals('Abnormal', $this->service->classifyEmosional(7));
    }

    /**
     * Test logika Prososial (Pr) (Terbalik: Skor rendah = buruk)
     */
    public function test_classify_prososial_is_correct(): void
    {
        // Rule: Normal (6-10)
        $this->assertEquals('Normal', $this->service->classifyPrososial(6));
        $this->assertEquals('Normal', $this->service->classifyPrososial(10));
        
        // Rule: Borderline (5)
        $this->assertEquals('Borderline', $this->service->classifyPrososial(5));
        
        // Rule: Abnormal (0-4)
        $this->assertEquals('Abnormal', $this->service->classifyPrososial(4));
        $this->assertEquals('Abnormal', $this->service->classifyPrososial(0));
    }
}
