import { AboutPageShell } from "@/app/components/about/about-page-shell";
import { LeadershipMessageContent } from "@/app/components/about/leadership/leadership-message-content";
import { fetchAboutLeadership } from "@/lib/api";
import { getTranslations, setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function DirectorMessagePage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("about.directorMessage");
  const apiData = await fetchAboutLeadership("director", locale);
  const paragraphs = apiData?.paragraphs ?? (t.raw("paragraphs") as string[]);
  const isRtl = locale === "ar";

  return (
    <AboutPageShell
      titleKey="directorMessage"
      image="/header/2.png"
      backgroundColor="#000000B8"
      heroClassName="max-lg:min-h-[200px] max-lg:sm:min-h-[220px]"
      heroContentClassName="max-lg:pb-6 max-lg:pt-20 max-lg:sm:pt-24"
      heroTitleClassName="max-lg:text-xl max-lg:leading-snug max-lg:sm:text-2xl"
    >
      <LeadershipMessageContent
        name={apiData?.name ?? t("name")}
        position={apiData?.position ?? t("position")}
        quote={apiData?.quote ?? t("quote")}
        paragraphs={paragraphs}
        image={apiData?.image ?? "/emp/2.png"}
        imageAlt={apiData?.imageAlt ?? t("imageAlt")}
        isRtl={isRtl}
        optimizeMobile
      />
    </AboutPageShell>
  );
}
