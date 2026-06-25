import { MediaIntro } from "@/app/components/media/media-intro";
import { NewsletterCardsGrid } from "@/app/components/media/newsletter/cards-grid";
import { fetchMediaArticles } from "@/lib/api";
import type { NewsletterItem } from "@/lib/media";
import { getLocale, getTranslations } from "next-intl/server";

export async function MediaNewsletterContent() {
  const t = await getTranslations("media");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const intro = {
    brand: t("newsletter.intro.brand"),
    brandEn: t("newsletter.intro.brandEn"),
    description: t("newsletter.intro.description"),
    bullets: t.raw("newsletter.intro.bullets") as string[],
  };

  const apiResult = await fetchMediaArticles("newsletter", locale, { limit: 50 });
  const fallbackItems = t.raw("newsletter.items") as NewsletterItem[];
  const items: NewsletterItem[] = apiResult
    ? (apiResult.items as NewsletterItem[])
    : fallbackItems;

  return (
    <>
      <MediaIntro
        brand={intro.brand}
        brandEn={intro.brandEn}
        description={intro.description}
        bullets={intro.bullets}
        isRtl={isRtl}
        accent="orange"
      />

      <NewsletterCardsGrid
        items={items}
        viewIssue={t("newsletter.viewIssue")}
        downloadPdf={t("newsletter.downloadPdf")}
        prevLabel={t("pagination.prev")}
        nextLabel={t("pagination.next")}
        isRtl={isRtl}
      />
    </>
  );
}
