import { getLocale, getTranslations } from "next-intl/server";
import { KnowledgeCenterHeader } from "@/app/components/home/knowledge-center/knowledge-center-header";
import { KnowledgeCenterCards } from "@/app/components/home/knowledge-center/knowledge-center-cards";
import type { HomePayload } from "@/lib/api";

const knowledgeIcons = ["icon1.png", "icon2.png", "icon3.png"] as const;
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
  const headerSlides = (knowledgeCenter?.headerSlides ?? headerContent).map(
    (slide, index) => ({
      ...slide,
      icon: knowledgeIcons[index] ?? knowledgeIcons[0],
    }),
  );

  const cardContent = t.raw("items") as CardContent[];
  const items = (knowledgeCenter?.items ?? cardContent).map((item, index) => ({
    title: item.title,
    date: item.date,
    href: item.href,
    pdfHref: item.pdfHref,
    image: knowledgeImages[index] ?? knowledgeImages[0],
    icon: knowledgeIcons[index] ?? knowledgeIcons[0],
  }));

  return (
    <section
      id="knowledge"
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-background py-12 sm:py-16 lg:py-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <KnowledgeCenterHeader slides={headerSlides} isRtl={isRtl} />

        <div className="mt-10 sm:mt-16 lg:mt-20">
          <KnowledgeCenterCards
            items={items}
            viewIssue={knowledgeCenter?.viewIssue ?? t("viewIssue")}
            downloadPdf={knowledgeCenter?.downloadPdf ?? t("downloadPdf")}
            isRtl={isRtl}
          />
        </div>
      </div>
    </section>
  );
}
