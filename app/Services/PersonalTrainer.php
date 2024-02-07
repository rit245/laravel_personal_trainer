<?php
namespace App\Services;

class PersonalTrainer {
    protected DietExpert $dietExpert;
    protected FitnessCoach $fitnessCoach;

    public function __construct() {
        $this->dietExpert = new DietExpert();
        $this->fitnessCoach = new FitnessCoach();
    }

    public function getRecommendation(String $solutionType, Array $lifeStyleTags): Array
    {
        $lifeStyleTagsArray = is_string($lifeStyleTags) ? explode(',', $lifeStyleTags) : $lifeStyleTags;
        $recommendations = [];

        /* solutionType + 공백값 체크 후 $recommendations array 에 값 추가 */
        if ($solutionType === 'DIET' || empty($solutionType)) {
            $dietRecommendations = $this->dietExpert->getRecommendations($lifeStyleTagsArray);
            $recommendations = array_merge($recommendations, $this->formatRecommendations($dietRecommendations, 'DIET'));
        }

        if ($solutionType === 'FITNESS' || empty($solutionType)) {
            $fitnessRecommendations = $this->fitnessCoach->getRecommendations($lifeStyleTagsArray);
            $recommendations = array_merge($recommendations, $this->formatRecommendations($fitnessRecommendations, 'FITNESS'));
        }

        return $recommendations;
    }

    protected function formatRecommendations(Array $recommendations, String $solutionType): Array
    {
        return array_map(function($recommendation) use ($solutionType) {
            return [
                'solutionType' => $solutionType,
                'recommendationName' => $recommendation['name'],
                /* 'tags' => $recommendation['tags'], // 태그는 리턴에서 제외 */
            ];
        }, $recommendations);
    }
}

/* 담당 전문가 클래스에 사용될 리턴 처리법 */
trait RecommendationsFilterTrait {
    protected function filterRecommendationsByTags(Array $solutions, Array $lifeStyleTags): Array
    {
        return array_filter($solutions, function($solution) use ($lifeStyleTags) {
            $matchingTags = array_intersect($solution['tags'], $lifeStyleTags);
            return !empty($matchingTags);
        });
    }
}

/* DIET 담당 전문가 클래스 - params: solutionType: DIET */
class DietExpert {
    use RecommendationsFilterTrait;

    public function getRecommendations(Array $lifeStyleTags): Array
    {
        $solutions = [
            ['name' => 'Intermittent Fasting', 'tags' => ['enough_time', 'strong_will']],
            ['name' => 'LCHF', 'tags' => ['enough_money']],
        ];
        return $this->filterRecommendationsByTags($solutions, $lifeStyleTags);
    }
}

/* FITNESS 담당 전문가 클래스 - params: solutionType: FITNESS */
class FitnessCoach {
    use RecommendationsFilterTrait;

    public function getRecommendations(Array $lifeStyleTags): Array
    {
        $solutions = [
            ['name' => 'Crossfit', 'tags' => ['enough_money', 'strong_will']],
            ['name' => 'Cardio Exercise', 'tags' => ['strong_will']],
            ['name' => 'Strength', 'tags' => ['strong_will', 'enough_time']],
            ['name' => 'Spinning', 'tags' => ['enough_money']],
        ];
        return $this->filterRecommendationsByTags($solutions, $lifeStyleTags);
    }
}
