import { getLocale, getTranslations } from "next-intl/server";
import Image from "next/image";

const statIcons = ["num1", "num2", "num3", "num4"] as const;

type StatItem = {
  value: string;
  label: string;
  description: string;
};

type Props = {
  title?: string;
  subtitle?: string;
  showSubtitle?: boolean;
  items?: StatItem[];
};

export async function StatsSection({
  title,
  subtitle,
  showSubtitle = true,
  items,
}: Props = {}) {
  const t = await getTranslations("home.stats");
  const locale = await getLocale();
  const isRtl = locale === "ar";
  const fallbackItems = t.raw("items") as StatItem[];
  const displayItems = items ?? fallbackItems;

  const displayTitle = title ?? t("title");
  const displaySubtitle = subtitle ?? t("subtitle");

  return (
    <section
      dir={isRtl ? "rtl" : "ltr"}
      className="bg-[#f8fafc] py-12 sm:py-16 lg:py-[95px]"
    >
      <div className="mx-auto max-w-7xl px-4 sm:px-6">
        <div className="mb-8 text-center sm:mb-10 lg:mb-[53px]">
          <h2
            className={
              showSubtitle
                ? "mb-3 text-2xl font-bold leading-tight text-secondary sm:mb-4 sm:text-3xl lg:mb-[26px] lg:text-4xl"
                : "text-2xl font-bold leading-tight text-secondary sm:text-3xl lg:text-4xl"
            }
          >
            {displayTitle}
          </h2>
          {showSubtitle ? (
            <p className="mx-auto max-w-[590px] px-1 text-sm leading-relaxed text-secondary sm:text-base lg:text-lg">
              {displaySubtitle}
            </p>
          ) : null}
        </div>

        <div className="mx-auto grid max-w-[1486px] grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:gap-[34px] xl:grid-cols-4">
          {displayItems.map((item, index) => (
            <article
              key={item.description}
              className="flex min-h-0 flex-col items-center rounded-2xl bg-white px-5 py-8 text-center shadow-[0_8px_32px_rgba(17,31,66,0.06)] sm:px-6 sm:py-9 lg:min-h-[335px] lg:px-7 lg:pb-10 lg:pt-[51px]"
            >
              <div className="mb-5 flex h-10 w-10 shrink-0 items-center justify-center sm:mb-6 sm:h-11 sm:w-11 lg:mb-[50px] lg:h-[47px] lg:w-[47px]">
                <Image
                  src={`/icons/${statIcons[index]}.svg`}
                  alt=""
                  width={47}
                  height={47}
                  className="h-10 w-10 object-contain sm:h-11 sm:w-11 lg:h-[47px] lg:w-[47px]"
                />
              </div>

              <p className="mb-3 flex flex-wrap items-baseline justify-center gap-x-1.5 gap-y-1 sm:mb-4 sm:gap-x-2 lg:mb-6">
                <span className="text-3xl font-bold leading-none text-primary sm:text-[2.25rem] lg:text-[2.5rem]">
                  {item.value}
                </span>
                <span className="text-base font-normal text-primary sm:text-lg lg:text-xl">
                  {item.label}
                </span>
              </p>

              <p className="text-sm leading-6 text-secondary sm:leading-7 lg:mt-auto lg:text-[0.95rem] lg:leading-8">
                {item.description}
              </p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
