<?php

use App\Models\AboutContent;
use App\Models\Program;
use App\Models\ProgramSection;
use App\Support\ProgramContentKey;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_section_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_section_id')->unique()->constrained('program_sections')->cascadeOnDelete();
            $table->text('intro_ar')->nullable();
            $table->text('intro_en')->nullable();
            $table->json('body_ar')->nullable();
            $table->json('body_en')->nullable();
            $table->timestamps();
        });

        Schema::table('about_content', function (Blueprint $table) {
            $table->foreignId('program_section_id')->nullable()->after('section_key')->unique()->constrained('program_sections')->nullOnDelete();
        });

        $this->migrateExistingSectionDetails();
        $this->migrateExistingSectionAboutContent();

        Schema::table('program_sections', function (Blueprint $table) {
            $table->dropColumn(['intro_ar', 'intro_en', 'body_ar', 'body_en']);
        });
    }

    public function down(): void
    {
        Schema::table('program_sections', function (Blueprint $table) {
            $table->text('intro_ar')->nullable()->after('title_en');
            $table->text('intro_en')->nullable()->after('intro_ar');
            $table->json('body_ar')->nullable()->after('intro_en');
            $table->json('body_en')->nullable()->after('body_ar');
        });

        foreach (DB::table('program_section_details')->orderBy('id')->get() as $detail) {
            DB::table('program_sections')
                ->where('id', $detail->program_section_id)
                ->update([
                    'intro_ar' => $detail->intro_ar,
                    'intro_en' => $detail->intro_en,
                    'body_ar' => $detail->body_ar,
                    'body_en' => $detail->body_en,
                ]);
        }

        Schema::table('about_content', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_section_id');
        });

        Schema::dropIfExists('program_section_details');
    }

    private function migrateExistingSectionDetails(): void
    {
        ProgramSection::query()->orderBy('id')->each(function (ProgramSection $section) {
            if ($section->intro_ar === null
                && $section->intro_en === null
                && $section->body_ar === null
                && $section->body_en === null) {
                return;
            }

            DB::table('program_section_details')->insert([
                'program_section_id' => $section->id,
                'intro_ar' => $section->intro_ar,
                'intro_en' => $section->intro_en,
                'body_ar' => $section->body_ar !== null ? json_encode($section->body_ar) : null,
                'body_en' => $section->body_en !== null ? json_encode($section->body_en) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    private function migrateExistingSectionAboutContent(): void
    {
        Program::query()->with('sections')->each(function (Program $program) {
            foreach ($program->sections as $section) {
                $sectionKey = ProgramContentKey::sectionKey($program->slug, $section->tab_key);
                $content = AboutContent::query()->where('section_key', $sectionKey)->first();

                if (! $content) {
                    if ($section->title_ar || $section->title_en || $section->image_url) {
                        AboutContent::query()->create([
                            'section_key' => 'program_section_'.$section->id,
                            'program_section_id' => $section->id,
                            'title_ar' => $section->title_ar,
                            'title_en' => $section->title_en,
                            'image_url' => $section->image_url,
                        ]);
                    }

                    continue;
                }

                $content->update([
                    'program_section_id' => $section->id,
                    'section_key' => 'program_section_'.$section->id,
                ]);
            }
        });
    }
};
