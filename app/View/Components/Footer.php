<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Schema;
use App\Models\FooterItem;

class Footer extends Component
{
    public array $socialLinks;
    public array $images;

    /**
     * Create the component instance.
     *
     * @param mixed|null $socialLinks
     * @param mixed|null $images
     */
    public function __construct($socialLinks = null, $images = null)
    {
        $this->socialLinks = $socialLinks ?? [
            ['platform' => 'twitter', 'url' => 'https://twitter.com', 'label' => 'Twitter'],
            ['platform' => 'facebook', 'url' => 'https://facebook.com', 'label' => 'Facebook'],
            ['platform' => 'linkedin', 'url' => 'https://linkedin.com', 'label' => 'LinkedIn'],
        ];

        $this->images = $images ?? [
            '/images/footer/logo1.svg',
            '/images/footer/logo2.svg',
        ];

        // If the footer_items table exists and contains data, use that instead of defaults.
        try {
            if (Schema::hasTable('footer_items')) {
                $dbSocials = FooterItem::socialLinks();
                $dbImages = FooterItem::imagesList();

                if (!empty($dbSocials)) {
                    $this->socialLinks = $dbSocials;
                }

                if (!empty($dbImages)) {
                    $this->images = $dbImages;
                }
            }
        } catch (\Exception $e) {
            // Ignore DB errors (e.g. during initial migrations)
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.footer');
    }
}
