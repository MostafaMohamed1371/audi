import { InnerPageShell } from "@/app/components/layout/inner-page-shell";
import { fetchLegalPage } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

type Props = {
  slug: "terms" | "privacy";
};

export async function LegalContent({ slug }: Props) {
  const t = await getTranslations("legal");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const page = await fetchLegalPage(slug, locale);
  const fallback = t.raw(slug) as { title: string; content: string };
  const title = page?.title ?? fallback.title;
  const content = page?.content ?? fallback.content;
  const paragraphs = content.split(/\n\n+/).filter(Boolean);

  return (
    <InnerPageShell title={title}>
      <article dir={isRtl ? "rtl" : "ltr"} className="mx-auto max-w-3xl">
        {page?.effectiveDate ? (
          <p className="mb-6 text-sm text-muted-foreground">
            {t("effectiveDate", { date: page.effectiveDate })}
          </p>
        ) : null}

        <div className="space-y-6">
          {paragraphs.map((paragraph) => (
            <p
              key={paragraph.slice(0, 48)}
              className="text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9"
            >
              {paragraph}
            </p>
          ))}
        </div>
      </article>
    </InnerPageShell>
  );
}
