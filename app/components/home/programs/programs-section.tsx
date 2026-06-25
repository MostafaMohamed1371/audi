import Image from "next/image";
import { getLocale, getTranslations } from "next-intl/server";
import { ButtonLink } from "@/app/components/ui/button";
import { Link } from "@/i18n/routing";
import { ChevronLeft } from "lucide-react";
import type { HomePayload } from "@/lib/api";

const programImages = ["p1.png", "p2.png", "p3.png"] as const;

function ProgramCardTitle({
  title,
  isRtl,
}: {
  title: string;
  isRtl: boolean;
}) {
  const words = title.trim().split(/\s+/);
  const firstWord = words.shift() ?? title;
  const restText = words.join(" ");

  return (
    <h3
      className={`mb-4 w-full text-xl font-bold capitalize leading-snug text-secondary sm:mb-4 sm:text-2xl ${isRtl ? "text-right" : "text-left"}`}
    >
      <span className="relative inline-block">
        <span
          className="pointer-events-none absolute start-0 top-1/2 -z-0 h-[23px] w-[70px] -translate-y-1/2 rounded-[41px] bg-[#00709E5C] sm:h-[26px] sm:w-[78px] sm:rounded-[46px]"
          aria-hidden
        />
        <span className="relative z-10">{firstWord}</span>
      </span>
      {restText ? <span> {restText}</span> : null}
    </h3>
  );
}

export async function ProgramsSection({
  programs,
}: {
  programs?: HomePayload["programs"];
} = {}) {
  const t = await getTranslations("home.programs");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const fallbackItems = t.raw("items") as {
    title: string;
    description: string;
    href: string;
  }[];
  const items = programs?.items ?? fallbackItems;

  return (
    <section
      id="programs"
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-background py-12 sm:py-16 lg:py-24"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <h2 className="mb-8 text-center text-2xl font-bold text-secondary sm:mb-12 sm:text-3xl lg:mb-14 lg:text-4xl">
          {programs?.title ?? t("title")}
        </h2>

        <div className="grid gap-6 sm:gap-8 md:grid-cols-2 lg:grid-cols-3 lg:gap-10">
          {items.map((item, index) => (
            <article
              key={item.title}
              className="flex flex-col overflow-hidden rounded-[24px] bg-white shadow-[1px_1px_18.6px_0px_#111F421C] sm:rounded-[30px]"
            >
              <div className="relative m-4 mb-0 aspect-[4/3] overflow-hidden rounded-2xl sm:m-5 sm:rounded-[20px]">
                <Image
                  src={`/projects/${programImages[index]}`}
                  alt={item.title}
                  fill
                  className="object-cover"
                  sizes="(max-width: 768px) 100vw, (max-width: 1280px) 50vw, 33vw"
                />
              </div>

              <div
                className={`flex flex-1 flex-col p-5 pt-4 sm:p-8 sm:pt-6 ${isRtl ? "items-start text-right" : "items-start text-left"}`}
              >
                <ProgramCardTitle title={item.title} isRtl={isRtl} />

                <p className="mb-6 flex-1 text-sm leading-7 text-[#4d5a6f] sm:mb-8 sm:text-[0.95rem] sm:leading-8">
                  {item.description}
                </p>

                <ButtonLink
                  size="lg"
                  className="rounded-full bg-primary px-7 hover:bg-primary/90"
                  render={
                    item.href.startsWith("/programs/") ? (
                      <Link href={item.href as "/programs/training"} />
                    ) : (
                      <a href={item.href} />
                    )
                  }
                >
                  {programs?.cta ?? t("cta")}
                  <ChevronLeft className="size-4" />
                </ButtonLink>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
