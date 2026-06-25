"use client";

import { Link, useRouter, type AppHref } from "@/i18n/routing";
import type { FocusAreaItem } from "@/lib/focus-areas";
import { ArrowLeft, ArrowRight, ChevronDown, ChevronUp } from "lucide-react";
import Image from "next/image";
import { useCallback, useEffect, useState } from "react";

const DETAIL_OVERLAY = "rgba(1, 9, 28, 0.88)";

type Props = {
  area: FocusAreaItem;
  backLabel: string;
  previousHref: AppHref | null;
  nextHref: AppHref | null;
  previousTitle: string | null;
  nextTitle: string | null;
  isRtl: boolean;
};

export function FocusAreaDetailHero({
  area,
  backLabel,
  previousHref,
  nextHref,
  previousTitle,
  nextTitle,
  isRtl,
}: Props) {
  const router = useRouter();
  const [isReady, setIsReady] = useState(false);

  useEffect(() => {
    setIsReady(false);
    const frame = window.requestAnimationFrame(() => {
      setIsReady(true);
    });
    return () => window.cancelAnimationFrame(frame);
  }, [area.slug]);

  const navigateSmooth = useCallback(
    (href: AppHref) => {
      type ViewTransitionDocument = Document & {
        startViewTransition?: (callback: () => void) => void;
      };

      const viewDocument = document as ViewTransitionDocument;
      if (typeof viewDocument.startViewTransition === "function") {
        viewDocument.startViewTransition(() => {
          router.push(href as Parameters<typeof router.push>[0], { scroll: false });
        });
        return;
      }

      router.push(href as Parameters<typeof router.push>[0], { scroll: false });
    },
    [router],
  );

  return (
    <section className="relative min-h-svh overflow-hidden">
      <Image
        src={area.detailImage}
        alt=""
        fill
        priority
        className="object-cover"
        sizes="100vw"
      />
      <div
        className="absolute inset-0"
        style={{ background: DETAIL_OVERLAY }}
        aria-hidden
      />

      <div
        className={`relative z-10 mx-auto flex min-h-svh max-w-7xl flex-col px-4 pb-16 pt-28 transition-opacity duration-300 sm:px-6 sm:pb-20 sm:pt-32 lg:px-8 ${isReady ? "opacity-100" : "opacity-0"
          }`}
      >
        <Link
          href="/strategy/focus-areas"
          dir="ltr"
          className="mb-10 inline-flex w-fit items-center gap-3 self-start text-lg font-bold text-white transition-opacity hover:opacity-80  w-full"
          onClick={(event) => {
            event.preventDefault();
            navigateSmooth("/strategy/focus-areas");
          }}
        >
          <span className="flex size-10 items-center justify-center rounded-full bg-primary">
            <ArrowRight className="size-5 rtl:rotate-180" />
          </span>
          {backLabel}
        </Link>

        <div 
          className={`w-full max-w-3xl space-y-4 lg:max-w-4xl`}
        >
          <div
            className={`flex items-center gap-4 rounded-[13px] border border-black/20 bg-[rgba(3,9,22,0.8)] p-5 sm:p-6  }`}
          >
            {nextHref ? (
              <Link
                href={nextHref}
                aria-label={nextTitle ?? area.title}
                className="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary transition-opacity hover:opacity-90"
                onClick={(event) => {
                  event.preventDefault();
                  navigateSmooth(nextHref);
                }}
              >
                <ChevronDown className="size-5 text-white" />
              </Link>
            ) : previousHref ? (
              <Link
                href={previousHref}
                aria-label={previousTitle ?? area.title}
                className="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary transition-opacity hover:opacity-90"
                onClick={(event) => {
                  event.preventDefault();
                  navigateSmooth(previousHref);
                }}
              >
                <ChevronUp className="size-5 text-white" />
              </Link>
            ) : (
              <span className="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/40 text-white/50">
                <ChevronDown className="size-5" />
              </span>
            )}

            <h1 className="text-start text-3xl font-bold text-white sm:text-4xl lg:text-5xl">
              {area.title}
            </h1>
          </div>

          <div className={`flex ${isRtl ? "justify-start" : "justify-end"}`}>
            {previousHref && previousTitle ? (
              <Link
                href={previousHref}
                className="inline-flex max-w-full items-center gap-3 rounded-[10px] bg-[rgba(3,9,22,0.86)] px-5 py-3 text-sm font-medium text-white/90 transition-opacity hover:opacity-85"
                onClick={(event) => {
                  event.preventDefault();
                  navigateSmooth(previousHref);
                }}
              >
                <span className="text-white">•</span>
                <span className="truncate">{previousTitle}</span>
              </Link>
            ) : (
              <div className="inline-flex max-w-full items-center gap-3 rounded-[10px] bg-[rgba(3,9,22,0.86)] px-5 py-3 text-sm font-medium text-white/85">
                <span className="text-white">•</span>
                <span className="truncate">{area.highlight}</span>
              </div>
            )}
          </div>
        </div>

        <p
          id="focus-area-description"
          dir={isRtl ? "rtl" : "ltr"}
          className="mx-auto mt-auto max-w-4xl pt-16 text-center text-lg leading-9 text-white sm:text-xl sm:leading-10 lg:pt-24"
        >
          {area.description}
        </p>
      </div>
    </section>
  );
}
