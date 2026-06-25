import { cn } from "@/lib/utils";

type HighlightedTitleProps = {
  title: string;
  as?: "p" | "h2" | "h3" | "span";
  className?: string;
  wrapperClassName?: string;
};

export function HighlightedTitle({
  title,
  as: Tag = "p",
  className,
  wrapperClassName,
}: HighlightedTitleProps) {
  return (
    <div className={cn("relative inline-block", wrapperClassName)}>
      <span
        aria-hidden
        className="absolute inset-x-0 bottom-1 h-2 rounded-full bg-[#D1E8F4]"
      />
      <Tag
        className={cn(
          "relative font-bold leading-snug text-primary",
          className,
        )}
      >
        {title}
      </Tag>
    </div>
  );
}
