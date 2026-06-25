import { PageHeroHeader } from "@/app/components/layout/page-hero-header";

type SectionPageShellProps = {
  title: string;
  placeholder: string;
  image?: string;
  backgroundColor?: string;
  children?: React.ReactNode;
};

export function SectionPageShell({
  title,
  placeholder,
  image,
  backgroundColor,
  children,
}: SectionPageShellProps) {
  return (
    <div className="bg-background">
      <PageHeroHeader
        title={title}
        image={image}
        backgroundColor={backgroundColor}
      />

      {children ?? (
        <section className="px-4 py-16 sm:px-6 sm:py-20">
          <div className="mx-auto max-w-4xl">
            <p className="text-base leading-8 text-muted-foreground sm:text-lg sm:leading-9">
              {placeholder}
            </p>
          </div>
        </section>
      )}
    </div>
  );
}
