import { FaqAccordion } from "@/app/components/faq/faq-accordion";
import { InnerPageShell } from "@/app/components/layout/inner-page-shell";
import { fetchFaqs } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

export async function FaqContent() {
  const t = await getTranslations("faq");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const apiItems = await fetchFaqs(locale);
  const fallbackItems = t.raw("fallback") as {
    id: number;
    question: string;
    answer: string;
  }[];
  const items = apiItems.length > 0 ? apiItems : fallbackItems;

  return (
    <InnerPageShell title={t("pages.title")} subtitle={t("pages.subtitle")}>
      {items.length === 0 ? (
        <p className="text-center text-muted-foreground">{t("empty")}</p>
      ) : (
        <FaqAccordion items={items} isRtl={isRtl} />
      )}
    </InnerPageShell>
  );
}
