<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ArticleSystemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Article::truncate();

        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            Category::create([
                'name'  => $faker->word,
                'key'   => $faker->word,
                'color' => '4a4a4a'
            ]);
        }

        $finalArr = [];
        for ($i = 1; $i <= 100; $i++) {
            Article::create([
                'title' => $faker->sentence(),
                'body'  => $faker->paragraphs(5, true) ,
            ]);

            // 关联的类别数
            $k           = mt_rand(1, 5);
            $categoryIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            shuffle($categoryIds);
            $categoryIds = array_slice($categoryIds, 0, $k);
            foreach ($categoryIds as $categoryIds) {
                $finalArr[] = [
                    'article_id'  => $i,
                    'category_id' => $categoryIds,
                ];
            }

        }

        DB::table('article_category')->insert($finalArr);

    }
}
