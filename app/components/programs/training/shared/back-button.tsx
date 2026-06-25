"use client";

import { Link, type AppHref } from "@/i18n/routing";
import { cn } from "@/lib/utils";
import { ArrowRight } from "lucide-react";

type Props = {
  label: string;
  href?: AppHref;
  onClick?: () => void;
  isRtl?: boolean;
};

export function TrainingBackButton({
  label,
  href = "/programs/training",
  onClick,
  isRtl = false,
}: Props) {
  const content = (
    <>
      <span className="text-lg font-bold text-secondary cursor-pointer mb-1">{label}</span>
      <span className="flex size-8 items-center justify-center rounded-full bg-primary cursor-pointer">
        <ArrowRight
          className={cn("size-4 text-white", isRtl ? "rotate-180" : "")}
        />
      </span>
    </>
  );

  if (onClick) {
    return (
      <button
        type="button"
        onClick={onClick}
        dir={isRtl ? "rtl" : "ltr"}
        className="mb-8 inline-flex w-fit items-center gap-3 transition-opacity hover:opacity-80 cursor-pointer"
      >
        {content}
      </button>
    );
  }

  return (
    <Link
      href={href}
      dir={isRtl ? "rtl" : "ltr"}
      className="mb-8 inline-flex w-fit items-center gap-3 transition-opacity hover:opacity-80 cursor-pointer"
    >
      {content}
    </Link>
  );
}
