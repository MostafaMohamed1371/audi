export const BLOG_IMAGES = ["1.png", "2.png", "3.png", "4.png"] as const;

export type MediaTab = "news" | "newsletter" | "cityMeetings" | "secretarySpeaks";

export type MediaArticleCategory = MediaTab;

export type NewsItem = {
  key: string;
  slug: string;
  title: string;
  description: string;
  date: string;
  image: string;
  body: string[];
};

export type NewsletterItem = {
  key: string;
  slug: string;
  title: string;
  date: string;
  image: string;
  pdfHref: string;
  body: string[];
};

export type CityMeetingItem = {
  key: string;
  slug: string;
  title: string;
  image: string;
  authors: string[];
  date: string;
  time: string;
  body: string[];
};

export type MediaArticle =
  | ({ category: "news" } & NewsItem)
  | ({ category: "secretarySpeaks" } & NewsItem)
  | ({ category: "newsletter" } & NewsletterItem)
  | ({ category: "cityMeetings" } & CityMeetingItem);

type MediaMessages = {
  news: { items: NewsItem[] };
  newsletter: { items: NewsletterItem[] };
  cityMeetings: { items: CityMeetingItem[] };
  secretarySpeaks?: { items: NewsItem[] };
};

export function getBlogImage(index: number) {
  return BLOG_IMAGES[index % BLOG_IMAGES.length];
}

export function getAllArticles(media: MediaMessages): MediaArticle[] {
  return [
    ...media.news.items.map((item) => ({ category: "news" as const, ...item })),
    ...(media.secretarySpeaks?.items ?? []).map((item) => ({
      category: "secretarySpeaks" as const,
      ...item,
    })),
    ...media.newsletter.items.map((item) => ({
      category: "newsletter" as const,
      ...item,
    })),
    ...media.cityMeetings.items.map((item) => ({
      category: "cityMeetings" as const,
      ...item,
    })),
  ];
}

export function getArticleBySlug(
  media: MediaMessages,
  slug: string,
  category?: MediaArticleCategory,
): MediaArticle | undefined {
  if (category) {
    if (category === "secretarySpeaks") {
      const item = media.secretarySpeaks?.items.find((entry) => entry.slug === slug);
      return item ? ({ category, ...item } as MediaArticle) : undefined;
    }

    const item = media[category].items.find((entry) => entry.slug === slug);
    return item ? ({ category, ...item } as MediaArticle) : undefined;
  }

  return getAllArticles(media).find((article) => article.slug === slug);
}

export function getSlugsByCategory(
  media: MediaMessages,
  category: MediaArticleCategory,
) {
  if (category === "secretarySpeaks") {
    return (media.secretarySpeaks?.items ?? []).map((item) => item.slug);
  }

  return media[category].items.map((item) => item.slug);
}

export function getAllSlugs(media: MediaMessages) {
  return getAllArticles(media).map((article) => article.slug);
}
