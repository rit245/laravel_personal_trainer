<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PersonalTrainer;

class WeightLossConsultingController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Recommendation",
     *     type="object",
     *     @OA\Property(
     *         property="solutionType",
     *         type="string",
     *         description="추천 유형 (DIET 또는 FITNESS)"
     *     ),
     *     @OA\Property(
     *         property="recommendationName",
     *         type="string",
     *         description="추천 프로그램 이름"
     *     )
     * )
     *
     * @OA\Get(
     *     path="/api/v1/how-to-lose-weight",
     *     operationId="getHowToLoseWeight",
     *     tags={"체중 조절 컨설팅"},
     *     summary="체중 조절 컨설팅 안내",
     *     description="사용자의 [생활 스타일 태그 / 선호하는 해결책 유형] 기반으로 체중 감량에 대한 개인화된 조언 제공",
     *     @OA\Parameter(
     *         name="solutionType",
     *         in="query",
     *         required=false,
     *         description="해결책 유형 (DIET / FITNESS / 공백값), 비워둘 경우 모든 유형의 추천을 제공",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="lifeStyleTags",
     *         in="query",
     *         required=true,
     *         description="사용자의 생활 스타일을 나타내는 태그, 쉼표로 구분",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="성공적으로 추천 정보를 반환",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="recommendations",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Recommendation")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="잘못된 요청",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 description="에러 메시지"
     *             )
     *         )
     *     )
     * )
     */

    public function howToLoseWeight(Request $request): \Illuminate\Http\JsonResponse
    {
        $solutionType = $request->input('solutionType');
        $lifeStyleTags = $request->input('lifeStyleTags');

        // solutionType 지정한 태그값 or 공백만 허용
        if (!($solutionType === 'DIET' || $solutionType === 'FITNESS' || empty($solutionType))) {
            return response()->json([
                'error' => '유효하지 않은 solutionType 값입니다.'
            ], 400);
        }

        // lifeStyleTags 값이 존재하지 않을 경우 400 에러 메시지 반환
        if (empty($lifeStyleTags)) {
            return response()->json([
                'error' => 'lifeStyleTags 값이 최소 1개 이상 있어야 합니다.'
            ], 400);
        }

        /* Tag 쪼개기 (조건 , ) */
        $lifeStyleTagsArray = explode(',', $lifeStyleTags);

        /* Service 추가 */
        $personalTrainer = new PersonalTrainer();
        $recommendations = $personalTrainer->getRecommendation($solutionType, $lifeStyleTagsArray);

        /* array_values 로 2차 에러 발생 방지 */
        return response()->json(['recommendations' => array_values($recommendations)]);

    }
}
