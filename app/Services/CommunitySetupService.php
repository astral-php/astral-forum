<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\ForumDao;
use App\Support\CommunitySettings;
use App\Support\Slug;

final class CommunitySetupService
{
    public function __construct(
        private CommunitySettings $settings,
        private ForumDao $forumDao,
        private ForumCatalogService $catalog,
    ) {
    }

    public function apply(string $name, string $tagline, string $generalDescription): void
    {
        $saved = $this->settings->save($name, $tagline, $generalDescription);
        $now = date('Y-m-d H:i:s');

        $general = $this->forumDao->findGeneral();
        if ($general !== null) {
            $this->forumDao->update($general->id, [
                'name' => CommunitySettings::GENERAL_NAME,
                'description' => $saved['general_description'] !== ''
                    ? $saved['general_description']
                    : null,
                'updated_at' => $now,
            ]);
        }

        $faq = $this->forumDao->findFaqForum();
        $faqDescription = $this->settings->faqDescription();
        if ($faq === null) {
            $slug = Slug::unique('faq', fn (string $s): bool => $this->forumDao->slugExists($s));
            $this->forumDao->insert([
                'name' => CommunitySettings::FAQ_NAME,
                'slug' => $slug,
                'description' => $faqDescription,
                'type' => 'faq',
                'status' => 'active',
                'position' => 20,
                'allow_member_replies' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $this->forumDao->update($faq->id, [
                'name' => CommunitySettings::FAQ_NAME,
                'description' => $faqDescription,
                'updated_at' => $now,
            ]);
        }

        $this->catalog->forgetPublicList();
    }
}
