import Image from "next/image";
import { cn } from "@/lib/utils";

type EmployeePortraitProps = {
  image: string;
  alt: string;
  coverImage?: string;
  className?: string;
  sizes?: string;
  priority?: boolean;
};

export function EmployeePortrait({
  image,
  alt,
  coverImage = "/emp/cover.png",
  className,
  sizes = "(max-width: 640px) 100vw, 280px",
  priority = false,
}: EmployeePortraitProps) {
  return (
    <div className={cn("relative aspect-4/5 w-full", className)}>
      <Image
        src={coverImage}
        alt=""
        aria-hidden
        fill
        sizes={sizes}
        className="object-contain object-bottom"
      />
      <Image
        src={image}
        alt={alt}
        fill
        sizes={sizes}
        priority={priority}
        className="z-10 object-contain object-bottom"
      />
    </div>
  );
}
