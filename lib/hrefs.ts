import type { AppHref } from "@/i18n/routing";
import type { MediaArticleCategory } from "@/lib/media";

const MEDIA_ARTICLE_PATHS = {
  news: "/media/news/[slug]",
  newsletter: "/media/newsletter/[slug]",
  cityMeetings: "/media/city-meetings/[slug]",
  secretarySpeaks: "/media/secretary-speaks/[slug]",
} as const;

export type MediaArticleHref = {
  pathname: (typeof MEDIA_ARTICLE_PATHS)[MediaArticleCategory];
  params: { slug: string };
};

export function mediaArticleHref(
  slug: string,
  category: MediaArticleCategory,
): MediaArticleHref {
  return {
    pathname: MEDIA_ARTICLE_PATHS[category],
    params: { slug },
  };
}

export function focusAreaHref(slug: string): AppHref {
  return { pathname: "/strategy/focus-areas/[slug]", params: { slug } };
}

export { directoryCityHref } from "@/lib/directory-cities";
