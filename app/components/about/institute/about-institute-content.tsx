import { InstituteHeadquartersCarousel } from "@/app/components/about/institute/institute-headquarters-carousel";
import { InstituteTasksSection } from "@/app/components/about/institute/institute-tasks-section";
import { StatsSection } from "@/app/components/home/stats/stats-section";
import { fetchAboutInstitute } from "@/lib/api";
import { getLocale, getTranslations } from "next-intl/server";

const headquartersImages = [
  "/InstituteHeadquarters/p1.png",
  "/InstituteHeadquarters/p2.png",
  "/InstituteHeadquarters/p3.png",
] as const;

export async function AboutInstituteContent() {
  const t = await getTranslations("about.institute");
  const tasksT = await getTranslations("about.tasks");
  const statsTitle = await getTranslations("about.stats");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const apiData = await fetchAboutInstitute(locale);

  const paragraphs = apiData?.paragraphs?.length
    ? apiData.paragraphs
    : [t("paragraph1"), t("paragraph2")];

  const tasks = (apiData?.tasks.items ?? tasksT.raw("items")) as {
    description: string;
  }[];

  return (
    <>
      <section className="bg-background py-16 sm:py-20 lg:py-24 xl:py-28">
        <div className="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[2fr_3fr] lg:items-start lg:gap-16 xl:gap-24">
          <h2 className="text-start text-2xl font-bold leading-snug text-primary sm:text-3xl lg:text-4xl xl:text-[2.5rem] xl:leading-tight">
            {apiData?.heading ?? t("heading")}
          </h2>

          <div className="space-y-6 text-start sm:space-y-8">
            {paragraphs.map((paragraph) => (
              <p
                key={paragraph}
                className="text-base leading-8 text-secondary sm:text-lg sm:leading-9"
              >
                {paragraph}
              </p>
            ))}
          </div>
        </div>
      </section>

      <StatsSection
        title={apiData?.statsTitle ?? statsTitle("title")}
        showSubtitle={false}
        items={apiData?.stats}
      />

      <InstituteHeadquartersCarousel
        images={[...headquartersImages]}
        title={apiData?.headquartersTitle ?? t("headquartersTitle")}
        isRtl={isRtl}
      />

      <InstituteTasksSection
        title={apiData?.tasks.title ?? tasksT("title")}
        items={tasks}
      />
    </>
  );
}
