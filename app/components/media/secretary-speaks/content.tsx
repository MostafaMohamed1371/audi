import { NewsCardsGrid } from "@/app/components/media/news/cards-grid";
import { fetchMediaArticles } from "@/lib/api";
import type { NewsItem } from "@/lib/media";
import { getLocale, getTranslations } from "next-intl/server";

export async function MediaSecretarySpeaksContent() {
  const t = await getTranslations("media");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const apiResult = await fetchMediaArticles("secretarySpeaks", locale, {
    limit: 50,
  });
  const fallbackItems = (t.raw("secretarySpeaks.items") as NewsItem[]) ?? [];
  const items: NewsItem[] = apiResult
    ? (apiResult.items as NewsItem[])
    : fallbackItems;

  return (
    <NewsCardsGrid
      items={items}
      readMore={t("detail.readMore")}
      prevLabel={t("pagination.prev")}
      nextLabel={t("pagination.next")}
      isRtl={isRtl}
      category="secretarySpeaks"
    />
  );
}
