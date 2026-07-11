import { DirectoryCityDetailShell } from "@/app/components/programs/urban-policies/sections/directory-city-detail-shell";
import type { DevelopmentPortalContent } from "@/app/components/programs/urban-policies/shared/types";
import { fetchDirectoryItem } from "@/lib/api";
import {
  DIRECTORY_CITY_SLUGS,
  directoryCityNumberFromSlug,
  isDirectoryCitySlug,
} from "@/lib/directory-cities";
import { getFallbackDirectoryCityDetail } from "@/lib/directory-city-detail";
import { getLocale, getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export function generateStaticParams() {
  return DIRECTORY_CITY_SLUGS.map((slug) => ({ slug }));
}

export default async function DirectoryCityDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  if (!isDirectoryCitySlug(slug)) {
    notFound();
  }

  const number = directoryCityNumberFromSlug(slug);
  if (!number) {
    notFound();
  }

  const t = await getTranslations("programs.urbanPolicies");
  const currentLocale = await getLocale();
  const isRtl = currentLocale === "ar";
  const directory = t.raw("developmentPortal.directory") as DevelopmentPortalContent["directory"];

  const apiData = await fetchDirectoryItem("cities", number, locale);
  const fallbackData = await getFallbackDirectoryCityDetail(locale, slug);
  const initialData = apiData ?? fallbackData;

  if (!initialData) {
    notFound();
  }

  return (
    <DirectoryCityDetailShell
      number={number}
      isRtl={isRtl}
      directoryUi={directory}
      initialData={initialData}
    />
  );
}
