<?php

namespace Tests\Feature;

use App\Models\CaseStudy;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NewModulesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_it_can_manage_faq_categories_via_admin_api()
    {
        Sanctum::actingAs($this->admin);

        // 1. Create Category
        $response = $this->postJson('/api/admin/faq-categories', [
            'name' => [
                'en' => 'General Questions',
                'ar' => 'أسئلة عامة'
            ],
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.category.name', 'General Questions')
            ->assertJsonPath('data.category.name_en', 'General Questions')
            ->assertJsonPath('data.category.name_ar', 'أسئلة عامة');

        $categoryId = $response->json('data.category.id');

        // 2. List Categories
        $response = $this->getJson('/api/admin/faq-categories?search=General');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.categories');

        // 3. Show Category
        $response = $this->getJson("/api/admin/faq-categories/{$categoryId}");
        $response->assertStatus(200)
            ->assertJsonPath('data.category.name', 'General Questions');

        // 4. Update Category
        $response = $this->postJson("/api/admin/faq-categories/{$categoryId}", [
            'name' => [
                'en' => 'Updated Category',
                'ar' => 'تصنيف محدث'
            ],
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.category.name_en', 'Updated Category');

        // 5. Delete Category
        $response = $this->deleteJson("/api/admin/faq-categories/{$categoryId}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('faq_categories', ['id' => $categoryId]);
    }

    public function test_it_can_manage_faqs_via_admin_api()
    {
        Sanctum::actingAs($this->admin);

        $category = FaqCategory::create([
            'name' => ['en' => 'Sales', 'ar' => 'المبيعات']
        ]);

        // 1. Create FAQ
        $response = $this->postJson('/api/admin/faqs', [
            'faq_category_id' => $category->id,
            'title' => [
                'en' => 'What is the price?',
                'ar' => 'ما هو السعر؟'
            ],
            'answer' => [
                'en' => 'It is free.',
                'ar' => 'إنه مجاني.'
            ],
            'order' => 5,
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.faq.title', 'What is the price?')
            ->assertJsonPath('data.faq.answer_ar', 'إنه مجاني.')
            ->assertJsonPath('data.faq.order', 5);

        $faqId = $response->json('data.faq.id');

        // 2. List FAQs
        $response = $this->getJson('/api/admin/faqs?category_id=' . $category->id);
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.faqs');

        // 3. Show FAQ
        $response = $this->getJson("/api/admin/faqs/{$faqId}");
        $response->assertStatus(200)
            ->assertJsonPath('data.faq.title_en', 'What is the price?');

        // 4. Update FAQ
        $response = $this->postJson("/api/admin/faqs/{$faqId}", [
            'title' => [
                'en' => 'Updated title?',
                'ar' => 'عنوان محدث؟'
            ]
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.faq.title_en', 'Updated title?');

        // 5. Delete FAQ
        $response = $this->deleteJson("/api/admin/faqs/{$faqId}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('faqs', ['id' => $faqId]);
    }

    public function test_it_can_manage_partners_via_admin_api()
    {
        Storage::fake('public');
        Sanctum::actingAs($this->admin);

        // 1. Create Partner with image
        $image = UploadedFile::fake()->image('partner_logo.jpg');

        $response = $this->postJson('/api/admin/partners', [
            'title' => ['en' => 'Partner A', 'ar' => 'شريك أ'],
            'subtitle' => ['en' => 'Tech Partner', 'ar' => 'شريك تقني'],
            'description' => ['en' => 'Provides cloud solutions', 'ar' => 'يوفر حلول سحابية'],
            'img' => $image,
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.partner.title_en', 'Partner A')
            ->assertJsonPath('data.partner.subtitle_ar', 'شريك تقني');

        $this->assertNotNull($response->json('data.partner.img_url'));
        $partnerId = $response->json('data.partner.id');
        $imgPath = $response->json('data.partner.img_path');

        Storage::disk('public')->assertExists($imgPath);

        // 2. List Partners
        $response = $this->getJson('/api/admin/partners?search=Partner');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.partners');

        // 3. Show Partner
        $response = $this->getJson("/api/admin/partners/{$partnerId}");
        $response->assertStatus(200);

        // 4. Update Partner
        $response = $this->postJson("/api/admin/partners/{$partnerId}", [
            'title' => ['en' => 'Partner A Updated', 'ar' => 'شريك أ محدث'],
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.partner.title_en', 'Partner A Updated');

        // 5. Delete Partner
        $response = $this->deleteJson("/api/admin/partners/{$partnerId}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('partners', ['id' => $partnerId]);
    }

    public function test_it_can_manage_case_studies_via_admin_api()
    {
        Storage::fake('public');
        Sanctum::actingAs($this->admin);

        $image = UploadedFile::fake()->image('case_study.png');

        // 1. Create Case Study
        $response = $this->postJson('/api/admin/case-studies', [
            'title' => ['en' => 'Project Alpha', 'ar' => 'مشروع ألفا'],
            'description' => ['en' => 'Success story', 'ar' => 'قصة نجاح'],
            'tags' => [
                'en' => ['IoT', 'Cloud'],
                'ar' => ['إنترنت الأشياء', 'سحابة']
            ],
            'img' => $image,
            'is_active' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.case_study.title_en', 'Project Alpha')
            ->assertJsonPath('data.case_study.tags_en', ['IoT', 'Cloud'])
            ->assertJsonPath('data.case_study.tags_ar', ['إنترنت الأشياء', 'سحابة']);

        $caseStudyId = $response->json('data.case_study.id');
        $imgPath = $response->json('data.case_study.img_path');

        Storage::disk('public')->assertExists($imgPath);

        // 2. List Case Studies
        $response = $this->getJson('/api/admin/case-studies?search=Alpha');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.case_studies');

        // 3. Show Case Study
        $response = $this->getJson("/api/admin/case-studies/{$caseStudyId}");
        $response->assertStatus(200);

        // 4. Update Case Study
        $response = $this->postJson("/api/admin/case-studies/{$caseStudyId}", [
            'tags' => [
                'en' => ['Web', 'Mobile'],
                'ar' => ['ويب', 'موبايل']
            ]
        ]);
        $response->assertStatus(200)
            ->assertJsonPath('data.case_study.tags_en', ['Web', 'Mobile']);

        // 5. Delete Case Study
        $response = $this->deleteJson("/api/admin/case-studies/{$caseStudyId}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('case_studies', ['id' => $caseStudyId]);
    }

    public function test_it_exposes_public_api_for_the_modules()
    {
        $category = FaqCategory::create(['name' => ['en' => 'Billing', 'ar' => 'الفواتير']]);
        Faq::create([
            'faq_category_id' => $category->id,
            'title' => ['en' => 'Question 1', 'ar' => 'سؤال 1'],
            'answer' => ['en' => 'Answer 1', 'ar' => 'جواب 1'],
            'order' => 1,
            'is_active' => true,
        ]);
        Faq::create([
            'faq_category_id' => $category->id,
            'title' => ['en' => 'Question 2', 'ar' => 'سؤال 2'],
            'answer' => ['en' => 'Answer 2', 'ar' => 'جواب 2'],
            'order' => 2,
            'is_active' => false, // inactive
        ]);

        Partner::create([
            'title' => ['en' => 'Partner 1', 'ar' => 'شريك 1'],
            'subtitle' => ['en' => 'Subtitle 1', 'ar' => 'فرعي 1'],
            'description' => ['en' => 'Desc 1', 'ar' => 'وصف 1'],
            'is_active' => true,
        ]);

        CaseStudy::create([
            'title' => ['en' => 'Case 1', 'ar' => 'حالة 1'],
            'description' => ['en' => 'Desc 1', 'ar' => 'وصف 1'],
            'tags' => ['en' => ['Tag A'], 'ar' => ['وسم أ']],
            'is_active' => true,
        ]);

        // 1. Test public FAQs
        $response = $this->getJson('/api/faqs');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.faqs') // only active
            ->assertJsonPath('data.faqs.0.title', 'Question 1');

        // Test public FAQ categories
        $response = $this->getJson('/api/faqs/categories');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.categories');

        // 2. Test public Partners
        $response = $this->getJson('/api/partners');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.partners');

        // 3. Test public Case Studies
        $response = $this->getJson('/api/case-studies');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.case_studies');
    }
}
