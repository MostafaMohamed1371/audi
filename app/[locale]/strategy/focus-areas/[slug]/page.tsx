import { FocusAreaDetailHero } from "@/app/components/strategy/focus-areas/focus-area-detail-hero";
import { fetchFocusArea } from "@/lib/api";
import {
  getAllFocusAreaSlugs,
  getFocusAreaBySlug,
  type FocusAreasMessages,
} from "@/lib/focus-areas";
import { focusAreaHref } from "@/lib/hrefs";
import { getLocale, getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export async function generateStaticParams() {
  const messages = (await import(`../../../../../messages/ar/strategy.json`))
    .default as { focusAreas: FocusAreasMessages };

  return getAllFocusAreaSlugs(messages.focusAreas).map((slug) => ({ slug }));
}

export default async function FocusAreaDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const apiData = await fetchFocusArea(slug, locale);
  const strategyMessages = (await import(`../../../../../messages/${locale}/strategy.json`))
    .default as { focusAreas: FocusAreasMessages };
  const fallbackArea = getFocusAreaBySlug(strategyMessages.focusAreas, slug);

  const area = apiData?.area ?? fallbackArea;

  if (!area) {
    notFound();
  }

  const t = await getTranslations("strategy.focusAreas.pages");
  const currentLocale = await getLocale();
  const isRtl = currentLocale === "ar";

  const previousArea = apiData?.navigation.previous
    ? { slug: apiData.navigation.previous.slug, title: apiData.navigation.previous.title }
    : (() => {
        const allAreas = strategyMessages.focusAreas.items;
        const currentIndex = allAreas.findIndex((item) => item.slug === area.slug);
        return currentIndex > 0 ? allAreas[currentIndex - 1] : null;
      })();

  const nextArea = apiData?.navigation.next
    ? { slug: apiData.navigation.next.slug, title: apiData.navigation.next.title }
    : (() => {
        const allAreas = strategyMessages.focusAreas.items;
        const currentIndex = allAreas.findIndex((item) => item.slug === area.slug);
        return currentIndex >= 0 && currentIndex < allAreas.length - 1
          ? allAreas[currentIndex + 1]
          : null;
      })();

  return (
    <FocusAreaDetailHero
      area={area}
      backLabel={t("back")}
      previousHref={previousArea ? focusAreaHref(previousArea.slug) : null}
      nextHref={nextArea ? focusAreaHref(nextArea.slug) : null}
      previousTitle={previousArea?.title ?? null}
      nextTitle={nextArea?.title ?? null}
      isRtl={isRtl}
    />
  );
}
