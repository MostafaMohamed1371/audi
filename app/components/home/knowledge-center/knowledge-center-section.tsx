import { getLocale, getTranslations } from "next-intl/server";
import {
  KnowledgeCenterBlock,
  knowledgeIcons,
} from "@/app/components/home/knowledge-center/knowledge-center-block";
import type { HomePayload } from "@/lib/api";
import { resolveImageSrc } from "@/lib/image-src";

const knowledgeImages = ["image1.png", "image2.png", "image3.png"] as const;

type HeaderSlideContent = {
  title: string;
  description: string;
};

type CardContent = {
  title: string;
  date: string;
  href: string;
  pdfHref: string;
  image?: string;
};

export async function KnowledgeCenterSection({
  knowledgeCenter,
}: {
  knowledgeCenter?: HomePayload["knowledgeCenter"];
} = {}) {
  const t = await getTranslations("home.knowledgeCenter");
  const locale = await getLocale();
  const isRtl = locale === "ar";

  const headerContent = t.raw("headerSlides") as HeaderSlideContent[];
  const apiSlides = knowledgeCenter?.categories?.length
    ? knowledgeCenter.categories.map((category, index) => ({
        id: category.id,
        slug: category.slug,
        title: category.title,
        description: category.description,
        icon: knowledgeIcons[index] ?? knowledgeIcons[0],
      }))
    : (knowledgeCenter?.headerSlides ?? headerContent).map((slide, index) => ({
        ...slide,
        icon: knowledgeIcons[index] ?? knowledgeIcons[0],
      }));

  const cardContent = t.raw("items") as CardContent[];

  const mapItems = (rawItems: CardContent[], iconIndex = 0) =>
    rawItems.map((item, index) => ({
      title: item.title,
      date: item.date,
      href: item.href,
      pdfHref: item.pdfHref,
      image:
        resolveImageSrc(item.image) ||
        `/knowledgeCenter/${knowledgeImages[index] ?? knowledgeImages[0]}`,
      icon: knowledgeIcons[(iconIndex + index) % knowledgeIcons.length] ?? knowledgeIcons[0],
    }));

  const categories =
    knowledgeCenter?.categories?.map((category, index) => ({
      items: mapItems(
        category.items.map((item) => ({
          title: item.title,
          date: item.date,
          href: item.href,
          pdfHref: item.pdfHref,
          image: item.image,
        })),
        index,
      ),
    })) ?? [];

  const fallbackItems = mapItems(
    (knowledgeCenter?.items ?? cardContent).map((item) => ({
      title: item.title,
      date: item.date,
      href: item.href,
      pdfHref: item.pdfHref,
      image: item.image,
    })),
  );

  return (
    <section
      id="knowledge"
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-background py-12 sm:py-16 lg:py-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <KnowledgeCenterBlock
          slides={apiSlides}
          categories={categories}
          fallbackItems={fallbackItems}
          viewIssue={knowledgeCenter?.viewIssue ?? t("viewIssue")}
          downloadPdf={knowledgeCenter?.downloadPdf ?? t("downloadPdf")}
          isRtl={isRtl}
        />
      </div>
    </section>
  );
}
