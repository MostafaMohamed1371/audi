import { AboutPageShell } from "@/app/components/about/about-page-shell";
import { LeadershipMessageContent } from "@/app/components/about/leadership/leadership-message-content";
import { fetchAboutLeadership } from "@/lib/api";
import { getTranslations, setRequestLocale } from "next-intl/server";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function PresidentSpeechPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("about.presidentSpeech");
  const apiData = await fetchAboutLeadership("president", locale);
  const paragraphs = apiData?.paragraphs ?? (t.raw("paragraphs") as string[]);
  const isRtl = locale === "ar";

  return (
    <AboutPageShell
      titleKey="presidentSpeech"
      image="/header/2.png"
      backgroundColor="#000000B8"
    >
      <LeadershipMessageContent
        honorific={apiData?.honorific ?? t("honorific")}
        name={apiData?.name ?? t("name")}
        position={apiData?.position ?? t("position")}
        quote={apiData?.quote ?? t("quote")}
        paragraphs={paragraphs}
        image={apiData?.image ?? "/emp/1.png"}
        imageAlt={apiData?.imageAlt ?? t("imageAlt")}
        isRtl={isRtl}
      />
    </AboutPageShell>
  );
}
