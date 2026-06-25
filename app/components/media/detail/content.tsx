import Image from "next/image";
import { Link } from "@/i18n/routing";
import { ArrowRight } from "lucide-react";
import type { MediaArticle } from "@/lib/media";

type Props = {
  article: MediaArticle;
  backLabel: string;
  isRtl: boolean;
};

function parseDateParts(date: string) {
  const parts = date.trim().split(/\s+/);

  if (parts.length >= 3) {
    return {
      day: parts[0]?.replace(/\D/g, "") || "01",
      monthYear: `${parts[1]} ${parts[2]}`,
    };
  }

  if (date.includes("/")) {
    const [day, month, year] = date.split("/");
    const months = [
      "JAN",
      "FEB",
      "MAR",
      "APR",
      "MAY",
      "JUN",
      "JUL",
      "AUG",
      "SEP",
      "OCT",
      "NOV",
      "DEC",
    ];
    const monthLabel = months[Number(month) - 1] ?? "JAN";

    return {
      day: day?.padStart(2, "0") ?? "01",
      monthYear: `${monthLabel} ${year}`,
    };
  }

  return { day: "01", monthYear: date };
}

export function MediaDetailContent({ article, backLabel, isRtl }: Props) {
  const dateSource =
    article.category === "cityMeetings" ? article.date : article.date;
  const { day, monthYear } = parseDateParts(dateSource);

  return (
    <article dir={isRtl ? "rtl" : "ltr"} className="mx-auto max-w-4xl">
      <Link
        href={
          article.category === "news"
            ? "/media/news"
            : article.category === "newsletter"
              ? "/media/newsletter"
              : article.category === "secretarySpeaks"
                ? "/media/secretary-speaks"
                : "/media/city-meetings"
        }
        className="mb-8 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
      >
        <ArrowRight className="size-4 rtl:rotate-180" />
        {backLabel}
      </Link>

      <header className="mb-8 space-y-6">
        <h1 className="text-start text-2xl font-bold leading-snug text-secondary sm:text-3xl lg:text-4xl">
          {article.title}
        </h1>

        <div className="flex items-end gap-3 text-primary">
          <span className="text-5xl font-bold leading-none sm:text-6xl">
            {day}
          </span>
          <span className="pb-1 text-sm font-semibold tracking-wide uppercase sm:text-base">
            {monthYear}
          </span>
        </div>
      </header>

      <div className="relative mb-10 aspect-[16/9] overflow-hidden rounded-2xl">
        <Image
          src={`/blog/${article.image}`}
          alt={article.title}
          fill
          className="object-cover"
          sizes="(max-width: 896px) 100vw, 896px"
          priority
        />
      </div>

      <div className="space-y-6 text-start">
        {article.body.map((paragraph) => (
          <p
            key={paragraph.slice(0, 40)}
            className="text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9"
          >
            {paragraph}
          </p>
        ))}
      </div>
    </article>
  );
}
