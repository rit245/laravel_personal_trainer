<?php

namespace Tests\Feature;

use Tests\TestCase;

class WeightLossConsultingTest extends TestCase
{

    /** @test */
    public function 리턴값_정상처리_테스트()
    {
        $response = $this->getJson('/api/v1/how-to-lose-weight?solutionType=DIET&lifeStyleTags=enough_money,strong_will');

        $response->assertStatus(200)
            ->assertJsonStructure([
                                      'recommendations' => [
                                          '*' => [
                                              'solutionType',
                                              'recommendationName'
                                          ]
                                      ]
                                  ]);
    }

    /** @test */
    public function 유효한_파라미터_solutionType_값_테스트()
    {
        $response = $this->getJson('/api/v1/how-to-lose-weight?solutionType=WRONG_TYPE&lifeStyleTags=enough_money,strong_will');

        $response->assertStatus(400)
            ->assertJson(['error' => '유효하지 않은 solutionType 값입니다.']);
    }

    /** @test */
    public function 최소_1개_파라미터_lifeStyleTags_값_테스트()
    {
        $response = $this->getJson('/api/v1/how-to-lose-weight?solutionType=DIET&lifeStyleTags=');

        $response->assertStatus(400)
            ->assertJson(['error' => 'lifeStyleTags 값이 최소 1개 이상 있어야 합니다.']);
    }
}
