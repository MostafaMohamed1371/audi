import { CityMeetingsCardsGrid } from "@/app/components/media/city-meetings/cards-grid";
import { MediaIntro } from "@/app/components/media/media-intro";
import { fetchMediaArticles } from "@/lib/api";
import type { CityMeetingItem } from "@/lib/media";
import { getLocale, getTranslations } from "next-intl/server";

export async function MediaCityMeetingsContent() {
  const t = await getTranslations("media");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const intro = {
    brand: t("cityMeetings.intro.brand"),
    description: t("cityMeetings.intro.description"),
  };

  const apiResult = await fetchMediaArticles("cityMeetings", locale, { limit: 50 });
  const fallbackItems = t.raw("cityMeetings.items") as CityMeetingItem[];
  const items: CityMeetingItem[] = apiResult
    ? (apiResult.items as CityMeetingItem[])
    : fallbackItems;

  return (
    <>
      <MediaIntro
        brand={intro.brand}
        description={intro.description}
        isRtl={isRtl}
        accent="blue"
      />

      <CityMeetingsCardsGrid
        items={items}
        watchLabel={t("cityMeetings.watch")}
        prevLabel={t("pagination.prev")}
        nextLabel={t("pagination.next")}
        isRtl={isRtl}
      />
    </>
  );
}
