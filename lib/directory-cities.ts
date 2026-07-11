import type { AppHref } from "@/i18n/routing";

export type DirectoryCitySlug =
  | "al-baha"
  | "riyadh"
  | "jeddah"
  | "cairo"
  | "amman"
  | "beirut";

export const DIRECTORY_CITY_SLUGS: DirectoryCitySlug[] = [
  "al-baha",
  "riyadh",
  "jeddah",
  "cairo",
  "amman",
  "beirut",
];

const SLUG_TO_NUMBER: Record<DirectoryCitySlug, string> = {
  "al-baha": "01",
  riyadh: "02",
  jeddah: "03",
  cairo: "04",
  amman: "05",
  beirut: "06",
};

const NUMBER_TO_SLUG: Record<string, DirectoryCitySlug> = Object.fromEntries(
  Object.entries(SLUG_TO_NUMBER).map(([slug, number]) => [number, slug]),
) as Record<string, DirectoryCitySlug>;

export function isDirectoryCitySlug(value: string): value is DirectoryCitySlug {
  return DIRECTORY_CITY_SLUGS.includes(value as DirectoryCitySlug);
}

export function directoryCityNumberFromSlug(slug: string): string | null {
  return isDirectoryCitySlug(slug) ? SLUG_TO_NUMBER[slug] : null;
}

export function directoryCitySlugFromNumber(number: string): DirectoryCitySlug | null {
  return NUMBER_TO_SLUG[number] ?? null;
}

export function directoryCityHref(slug: DirectoryCitySlug): AppHref {
  return {
    pathname: "/programs/urban-policies/development-portal/cities/[slug]",
    params: { slug },
  };
}
