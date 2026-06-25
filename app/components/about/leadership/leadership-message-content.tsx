import Image from "next/image";
import { HighlightedTitle } from "@/app/components/ui/highlighted-title";
import { cn } from "@/lib/utils";

type Props = {
  honorific?: string;
  name: string;
  position: string;
  quote: string;
  paragraphs: string[];
  image?: string;
  imageAlt: string;
  isRtl?: boolean;
  /** Mobile-only layout tweaks (max-lg). Desktop unchanged. */
  optimizeMobile?: boolean;
};

export function LeadershipMessageContent({
  honorific,
  name,
  position,
  quote,
  paragraphs,
  image = "/emp/1.png",
  imageAlt,
  isRtl = true,
  optimizeMobile = false,
}: Props) {
  return (
    <section
      className={cn(
        "bg-white py-20 sm:py-24 lg:py-32",
        optimizeMobile && "max-lg:overflow-x-hidden max-lg:py-10",
      )}
    >
      <div
        className={cn(
          "mx-auto max-w-7xl px-4 sm:px-6",
          optimizeMobile && "max-lg:px-4",
        )}
      >
        <div
          dir={isRtl ? "rtl" : "ltr"}
          className={cn(
            "grid grid-cols-1 items-start gap-14 lg:grid-cols-12 lg:gap-16 xl:gap-20",
            optimizeMobile && "max-lg:gap-8",
          )}
        >
          <div
            className={cn(
              "mx-auto w-full max-w-[360px] lg:col-span-4 lg:mx-0 lg:max-w-none",
              optimizeMobile && "max-lg:max-w-[220px] max-lg:text-center",
            )}
          >
            <div
              className={cn(
                "relative aspect-4/5 w-full",
                optimizeMobile && "max-lg:aspect-3/4",
              )}
            >
              <Image
                src={image}
                alt={imageAlt}
                fill
                className="object-contain object-bottom"
                sizes={
                  optimizeMobile
                    ? "(max-width: 1024px) 220px, 360px"
                    : "(max-width: 1024px) 360px, 360px"
                }
                priority
              />
            </div>

            <div
              className={cn(
                "mt-5 space-y-4",
                optimizeMobile && "max-lg:mt-4 max-lg:space-y-2",
              )}
            >
              {honorific ? (
                <p
                  className={cn(
                    "text-base text-secondary sm:text-lg",
                    optimizeMobile && "max-lg:text-sm",
                  )}
                >
                  {honorific}
                </p>
              ) : null}

              <HighlightedTitle
                as="h2"
                title={name}
                wrapperClassName={
                  optimizeMobile ? "max-lg:block max-lg:w-full" : undefined
                }
                className={cn(
                  "text-[1.65rem] sm:text-[1.85rem]",
                  optimizeMobile &&
                    "max-lg:text-xl max-lg:leading-snug max-lg:sm:text-xl",
                )}
              />

              <p
                className={cn(
                  "font-bold text-secondary",
                  optimizeMobile && "max-lg:text-sm max-lg:leading-relaxed",
                )}
              >
                {position}
              </p>
            </div>
          </div>

          <div className="min-w-0 lg:col-span-8">
            <blockquote className="text-start">
              <HighlightedTitle
                title={quote}
                wrapperClassName={
                  optimizeMobile ? "max-lg:block max-lg:w-full" : undefined
                }
                className={cn(
                  "text-[1.6rem] leading-relaxed text-[#004B87]",
                  optimizeMobile && "max-lg:text-lg max-lg:leading-8",
                )}
              />
            </blockquote>

            <div
              className={cn(
                "mt-10 space-y-8 sm:mt-12 sm:space-y-10",
                optimizeMobile && "max-lg:mt-6 max-lg:space-y-5",
              )}
            >
              {paragraphs.map((paragraph, index) => (
                <p
                  key={index}
                  className={cn(
                    "text-start text-[0.95rem] leading-8 text-secondary sm:text-base sm:leading-[2.15rem]",
                    optimizeMobile &&
                      "max-lg:text-sm max-lg:leading-7 max-lg:sm:text-sm max-lg:sm:leading-7",
                  )}
                >
                  {paragraph}
                </p>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
