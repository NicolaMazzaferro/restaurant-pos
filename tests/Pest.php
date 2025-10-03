<?php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Importante:
 * - Bootstrappa la Laravel TestCase anche per i test in Unit/
 * - Applica RefreshDatabase a Feature E Unit (usiamo DB/Factories ovunque)
 */
uses(TestCase::class)->in('Feature', 'Unit');
uses(RefreshDatabase::class)->in('Feature', 'Unit');
