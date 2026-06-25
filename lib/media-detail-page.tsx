import { MediaDetailContent } from "@/app/components/media/detail/content";
import { redirect, routing } from "@/i18n/routing";
import { fetchMediaArticle } from "@/lib/api";
import { mediaArticleHref } from "@/lib/hrefs";
import {
  getArticleBySlug,
  getSlugsByCategory,
  type MediaArticle,
  type MediaArticleCategory,
} from "@/lib/media";
import { getLocale, getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";

type PageProps = {
  params: Promise<{ locale: string; slug: string }>;
};

function normalizeSlug(rawSlug: string) {
  try {
    return decodeURIComponent(rawSlug);
  } catch {
    return rawSlug;
  }
}

function mapApiArticle(
  payload: Record<string, unknown>,
  category: MediaArticleCategory,
): MediaArticle {
  const base = {
    key: String(payload.key ?? ""),
    slug: String(payload.slug ?? ""),
    title: String(payload.title ?? ""),
    date: String(payload.date ?? ""),
    image: String(payload.image ?? ""),
    body: (payload.body as string[]) ?? [],
  };

  if (category === "news" || category === "secretarySpeaks") {
    return {
      category,
      ...base,
      description: String(payload.description ?? ""),
    };
  }

  if (category === "newsletter") {
    return {
      category: "newsletter",
      ...base,
      pdfHref: String(payload.pdfHref ?? "#"),
    };
  }

  return {
    category: "cityMeetings",
    ...base,
    authors: (payload.authors as string[]) ?? [],
    time: String(payload.time ?? ""),
  };
}

export function createMediaDetailPage(category: MediaArticleCategory) {
  async function generateStaticParams() {
    const params: { locale: string; slug: string }[] = [];

    for (const locale of routing.locales) {
      const messages = (await import(`../messages/${locale}/media.json`))
        .default;

      for (const slug of getSlugsByCategory(messages, category)) {
        params.push({ locale, slug });
      }
    }

    return params;
  }

  async function MediaDetailPage({ params }: PageProps) {
    const { locale, slug: rawSlug } = await params;
    const slug = normalizeSlug(rawSlug);
    setRequestLocale(locale);

    const apiPayload = await fetchMediaArticle(category, slug, locale);
    let article: MediaArticle | undefined = apiPayload
      ? mapApiArticle(apiPayload, category)
      : undefined;

    const messages = (await import(`../messages/${locale}/media.json`)).default;

    if (!article) {
      article = getArticleBySlug(messages, slug, category);
    }

    if (!article) {
      const fallback = getArticleBySlug(messages, slug);
      if (fallback && fallback.category !== category) {
        redirect({
          href: mediaArticleHref(fallback.slug, fallback.category),
          locale,
        });
      }

      notFound();
    }

    const t = await getTranslations("media.detail");
    const currentLocale = await getLocale();
    const isRtl = currentLocale === "ar";

    return (
      <div className="bg-background px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        <MediaDetailContent
          article={article}
          backLabel={t("back")}
          isRtl={isRtl}
        />
      </div>
    );
  }

  return { generateStaticParams, default: MediaDetailPage };
}
