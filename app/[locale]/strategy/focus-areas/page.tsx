import { FocusAreasPage } from "@/app/components/strategy/focus-areas/focus-areas-page";
import { fetchFocusAreas } from "@/lib/api";
import type { FocusAreasMessages } from "@/lib/focus-areas";
import { getTranslations, setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function FocusAreasRoute({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("strategy");
  const fallbackContent = t.raw("focusAreas") as FocusAreasMessages;
  const apiData = await fetchFocusAreas(locale);
  const content: FocusAreasMessages = apiData
    ? {
        pages: {
          title: apiData.pages.title,
          back: apiData.pages.back,
          viewMore: apiData.pages.viewMore,
        },
        items: apiData.items as FocusAreasMessages["items"],
      }
    : fallbackContent;

  return <FocusAreasPage content={content} />;
}
