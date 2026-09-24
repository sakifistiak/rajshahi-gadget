<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\CustomPage;
use App\Models\PhilanthropicWork;
use App\Models\Product;
use App\Support\Seo;
use Carbon\Carbon;

/**
 * Builds /sitemap.xml from the database (SEO phase 1: C4).
 *
 * Only canonical, publicly reachable (HTTP 200) URLs are listed: no filtered/sorted shop URLs,
 * no cart/checkout/compare, nothing under /public. The static /about and /contact pages are left
 * out on purpose: they still hold template placeholder text, and the real ones are CMS pages
 * under /page/. One file holds up to 50,000 URLs; switch to a sitemap index (products / pages /
 * blog) before the catalogue gets near that.
 *
 * Built on demand, deliberately without a cache: it is a handful of cheap queries at this
 * catalogue size and is therefore always current, including after the admin bulk delete and the
 * stock CSV import (both use query-builder writes that fire no model events, so an
 * event-invalidated cache would go stale). Crawlers fetch it rarely; the response is
 * HTTP-cacheable. Add caching only if it ever shows up in profiling.
 */
class SitemapBuilder
{
    public function xml(): string
    {
        return $this->build(Seo::origin());
    }

    private function build(string $origin): string
    {
        $latestProduct = Product::query()->max('updated_at');
        $latestPost = BlogPost::published()->max('updated_at');
        $latestWork = PhilanthropicWork::query()->max('updated_at');

        // [path, lastmod]: lastmod is left out where no real timestamp exists.
        $entries = [
            ['/', $latestProduct],
            ['/shop', $latestProduct],
            ['/blog', $latestPost],
            ['/philanthropic-work', $latestWork],
            ['/customer-spotlight', null],
            ['/customer-feedback', null],
        ];

        // Category listings, only those that actually contain products (directly or via a child).
        $categories = Category::query()->get(['id', 'slug', 'parent_id', 'updated_at'])->keyBy('id');
        $used = [];
        foreach (Product::query()->whereNotNull('category_id')->distinct()->pluck('category_id') as $id) {
            while ($id && isset($categories[$id]) && ! isset($used[$id])) {
                $used[$id] = true;
                $id = $categories[$id]->parent_id;
            }
        }
        foreach ($categories as $category) {
            if (isset($used[$category->id]) && $category->slug) {
                $entries[] = [Seo::path('shop', $category->slug), $category->updated_at];
            }
        }

        foreach (Product::query()->select(['id', 'slug', 'updated_at'])->orderBy('id')->cursor() as $product) {
            if ($product->slug) {
                $entries[] = [Seo::path('product', $product->slug), $product->updated_at];
            }
        }

        foreach (CustomPage::query()->where('is_active', true)->orderBy('id')->get(['slug', 'updated_at']) as $page) {
            $entries[] = [Seo::path('page', $page->slug), $page->updated_at];
        }

        foreach (BlogPost::published()->orderBy('id')->get(['slug', 'updated_at']) as $post) {
            $entries[] = [Seo::path('blog', $post->slug), $post->updated_at];
        }

        foreach (PhilanthropicWork::query()->whereNotNull('slug')->orderBy('id')->get(['slug', 'updated_at']) as $work) {
            $entries[] = [Seo::path('philanthropic-work', $work->slug), $work->updated_at];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($entries as [$path, $updatedAt]) {
            $xml .= "  <url>\n    <loc>".$this->escape($origin.$path)."</loc>\n";
            if ($updatedAt) {
                $xml .= '    <lastmod>'.Carbon::parse($updatedAt)->toAtomString()."</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }

        return $xml.'</urlset>'."\n";
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
