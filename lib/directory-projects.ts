import type { AppHref } from "@/i18n/routing";

export type DirectoryProjectSlug =
  | "cairo"
  | "riyadh"
  | "kuwait"
  | "dubai"
  | "tunis"
  | "manama";

export const DIRECTORY_PROJECT_SLUGS: DirectoryProjectSlug[] = [
  "cairo",
  "riyadh",
  "kuwait",
  "dubai",
  "tunis",
  "manama",
];

const SLUG_TO_NUMBER: Record<DirectoryProjectSlug, string> = {
  cairo: "01",
  riyadh: "02",
  kuwait: "03",
  dubai: "04",
  tunis: "05",
  manama: "06",
};

export function isDirectoryProjectSlug(value: string): value is DirectoryProjectSlug {
  return DIRECTORY_PROJECT_SLUGS.includes(value as DirectoryProjectSlug);
}

export function directoryProjectNumberFromSlug(slug: string): string | null {
  return isDirectoryProjectSlug(slug) ? SLUG_TO_NUMBER[slug] : null;
}

export function directoryProjectHref(slug: DirectoryProjectSlug): AppHref {
  return {
    pathname: "/programs/urban-policies/development-portal/projects/[slug]",
    params: { slug },
  };
}
