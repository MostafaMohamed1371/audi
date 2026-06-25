import { pathnames } from "@/i18n/pathnames";
import arMedia from "@/messages/ar/media.json";
import enMedia from "@/messages/en/media.json";
import type { MediaArticleHref } from "@/lib/hrefs";
import { mediaArticleHref } from "@/lib/hrefs";
import type { MediaArticleCategory } from "@/lib/media";

type MediaItem = { key: string; slug: string };

const MEDIA_CATEGORIES: MediaArticleCategory[] = [
  "news",
  "newsletter",
  "cityMeetings",
];

const MEDIA_DETAIL_ROUTES = [
  { category: "news" as const, detailPath: "/media/news/[slug]" as const },
  {
    category: "newsletter" as const,
    detailPath: "/media/newsletter/[slug]" as const,
  },
  {
    category: "cityMeetings" as const,
    detailPath: "/media/city-meetings/[slug]" as const,
  },
];

function getAllItems(media: typeof arMedia): MediaItem[] {
  return [
    ...media.news.items,
    ...media.newsletter.items,
    ...media.cityMeetings.items,
  ];
}

function slugToKeyByLocale(locale: "ar" | "en") {
  const media = locale === "ar" ? arMedia : enMedia;
  const map = new Map<string, string>();

  for (const item of getAllItems(media)) {
    map.set(item.slug, item.key);
  }

  return map;
}

function keyToSlugByLocale(locale: "ar" | "en") {
  const media = locale === "ar" ? arMedia : enMedia;
  const map = new Map<string, string>();

  for (const item of getAllItems(media)) {
    map.set(item.key, item.slug);
  }

  return map;
}

function getDetailPrefixes(
  detailPath: (typeof MEDIA_DETAIL_ROUTES)[number]["detailPath"],
) {
  const localized = pathnames[detailPath];
  const internal = detailPath.replace("/[slug]", "");

  if (typeof localized === "string") {
    return [internal, localized];
  }

  return [internal, localized.ar, localized.en];
}

function safeDecode(value: string) {
  try {
    return decodeURIComponent(value);
  } catch {
    return value;
  }
}

function extractMediaArticleFromPath(
  pathname: string,
  routeSlug?: string,
): { category: MediaArticleCategory; slug: string } | null {
  for (const { category, detailPath } of MEDIA_DETAIL_ROUTES) {
    for (const prefix of getDetailPrefixes(detailPath)) {
      if (!pathname.startsWith(prefix)) {
        continue;
      }

      const suffix = pathname.slice(prefix.length);
      if (!suffix.startsWith("/")) {
        continue;
      }

      const slugPart = suffix.slice(1);
      if (!slugPart || slugPart === "[slug]") {
        if (routeSlug) {
          return { category, slug: safeDecode(routeSlug) };
        }
        continue;
      }

      return { category, slug: safeDecode(slugPart) };
    }
  }

  if (routeSlug) {
    for (const { category, detailPath } of MEDIA_DETAIL_ROUTES) {
      for (const prefix of getDetailPrefixes(detailPath)) {
        if (pathname === prefix) {
          return { category, slug: safeDecode(routeSlug) };
        }
      }
    }
  }

  return null;
}

export function getMediaArticleCategory(
  key: string,
): MediaArticleCategory | undefined {
  for (const category of MEDIA_CATEGORIES) {
    if (arMedia[category].items.some((item) => item.key === key)) {
      return category;
    }
  }

  return undefined;
}

export function getMediaArticleKeyFromSlug(slug: string, locale: "ar" | "en") {
  return slugToKeyByLocale(locale).get(slug);
}

export function getMediaArticleSlug(key: string, locale: "ar" | "en") {
  return keyToSlugByLocale(locale).get(key);
}

export function getMediaArticleHrefForLocale(
  key: string,
  locale: "ar" | "en",
): MediaArticleHref | null {
  const category = getMediaArticleCategory(key);
  const slug = getMediaArticleSlug(key, locale);

  if (!category || !slug) {
    return null;
  }

  return mediaArticleHref(slug, category);
}

export function resolveMediaLocaleSwitchHref(
  pathname: string,
  currentLocale: "ar" | "en",
  targetLocale: "ar" | "en",
  routeSlug?: string,
): MediaArticleHref | null {
  const article = extractMediaArticleFromPath(pathname, routeSlug);
  if (!article) {
    return null;
  }

  const key = getMediaArticleKeyFromSlug(article.slug, currentLocale);
  if (!key) {
    return null;
  }

  const targetSlug = getMediaArticleSlug(key, targetLocale);
  return targetSlug ? mediaArticleHref(targetSlug, article.category) : null;
}

export function getNewsSlugs(locale: "ar" | "en", count: number) {
  const media = locale === "ar" ? arMedia : enMedia;
  return media.news.items.slice(0, count).map((item) => item.slug);
}
