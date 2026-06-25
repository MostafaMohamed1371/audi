export type FocusAreaSlug =
  | "governance-and-institutional-development"
  | "urban-resilience"
  | "local-economic-development"
  | "cities-for-people";

export type FocusAreaItem = {
  slug: FocusAreaSlug;
  number: string;
  title: string;
  highlight: string;
  tags: string[];
  description: string;
  listImage: string;
  detailImage: string;
};

export type FocusAreasMessages = {
  pages: {
    title: string;
    back: string;
    viewMore: string;
  };
  items: FocusAreaItem[];
};

export const FOCUS_AREA_SLUGS: FocusAreaSlug[] = [
  "governance-and-institutional-development",
  "urban-resilience",
  "local-economic-development",
  "cities-for-people",
];

export function isFocusAreaSlug(value: string): value is FocusAreaSlug {
  return FOCUS_AREA_SLUGS.includes(value as FocusAreaSlug);
}

export function getFocusAreaBySlug(
  messages: FocusAreasMessages,
  slug: string,
): FocusAreaItem | undefined {
  return messages.items.find((item) => item.slug === slug);
}

export function getAllFocusAreaSlugs(messages: FocusAreasMessages) {
  return messages.items.map((item) => item.slug);
}
