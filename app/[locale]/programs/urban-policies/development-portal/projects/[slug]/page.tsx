import { DirectoryProjectDetailShell } from "@/app/components/programs/urban-policies/sections/directory-project-detail-shell";
import type { DevelopmentPortalContent } from "@/app/components/programs/urban-policies/shared/types";
import { fetchDirectoryItem } from "@/lib/api";
import {
  DIRECTORY_PROJECT_SLUGS,
  directoryProjectNumberFromSlug,
  isDirectoryProjectSlug,
} from "@/lib/directory-projects";
import { getFallbackDirectoryItem } from "@/lib/directory-item-fallback";
import { getLocale, getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export function generateStaticParams() {
  return DIRECTORY_PROJECT_SLUGS.map((slug) => ({ slug }));
}

export default async function DirectoryProjectDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  if (!isDirectoryProjectSlug(slug)) {
    notFound();
  }

  const number = directoryProjectNumberFromSlug(slug);
  if (!number) {
    notFound();
  }

  const t = await getTranslations("programs.urbanPolicies");
  const currentLocale = await getLocale();
  const isRtl = currentLocale === "ar";
  const directory = t.raw("developmentPortal.directory") as DevelopmentPortalContent["directory"];

  const apiData = await fetchDirectoryItem("projects", number, locale);
  const fallbackData = await getFallbackDirectoryItem(locale, "projects", number);
  const initialData = apiData ?? fallbackData;

  if (!initialData) {
    notFound();
  }

  return (
    <DirectoryProjectDetailShell
      number={number}
      isRtl={isRtl}
      directoryUi={directory}
      initialData={initialData}
    />
  );
}
