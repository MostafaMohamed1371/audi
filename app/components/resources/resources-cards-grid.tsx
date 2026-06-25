import Image from "next/image";
import { Calendar, Download } from "lucide-react";
import { cn } from "@/lib/utils";

export type ResourceItem = {
  slug: string;
  title: string;
  date: string;
  image: string;
  downloadHref: string;
  buttonVariant: "primary" | "secondary" | "light";
};

const buttonStyles = {
  primary: "bg-primary text-white hover:bg-primary/90",
  secondary: "bg-secondary text-white hover:bg-secondary/90",
  light: "bg-[#c5dce8] text-secondary hover:bg-[#b5d0df]",
} as const;

type Props = {
  items: ResourceItem[];
  downloadLabel: string;
  isRtl: boolean;
};

export function ResourcesCardsGrid({
  items,
  downloadLabel,
  isRtl,
}: Props) {
  return (
    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
      {items.map((item) => (
        <article
          key={item.slug}
          dir={isRtl ? "rtl" : "ltr"}
          className="flex h-full flex-col overflow-hidden rounded-[24px] border border-border/60 bg-white shadow-[1px_1px_18px_0px_#111F4214]"
        >
          <div className="relative aspect-[3/4] overflow-hidden">
            <Image
              src={`/our-sources/${item.image}`}
              alt={item.title}
              fill
              className="object-cover"
              sizes="(max-width: 1024px) 50vw, 25vw"
            />
          </div>

          <div className="flex flex-1 flex-col p-5 sm:p-6">
            <div className="mb-4 flex items-center justify-end gap-2 text-xs font-medium tracking-wide text-muted-foreground uppercase sm:text-sm">
              <span>{item.date}</span>
              <Calendar className="size-4 shrink-0 text-primary" />
            </div>

            <h3 className="mb-6 flex-1 text-start text-base font-bold leading-snug text-secondary sm:text-lg">
              {item.title}
            </h3>

            <a
              href={item.downloadHref}
              download
              className={cn(
                "inline-flex w-full items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-semibold transition-colors",
                buttonStyles[item.buttonVariant],
              )}
            >
              <Download className="size-4" />
              {downloadLabel}
            </a>
          </div>
        </article>
      ))}
    </div>
  );
}
