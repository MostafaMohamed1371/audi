type SocialIconProps = {
  name: "facebook" | "linkedin" | "youtube" | "instagram" | "x";
  className?: string;
};

export function SocialIcon({ name, className = "size-5" }: SocialIconProps) {
  const icons = {
    facebook: (
      <path d="M9 8h-3v4h3v12h5v-12h3.6l.4-4h-4v-1.7c0-1.1.3-1.7 1.7-1.7h2.3v-4h-3.2c-3.1 0-4.5 1.5-4.5 4.3v2.1z" />
    ),
    linkedin: (
      <path d="M6.5 9h4v12h-4V9zm2-6c1.3 0 2.3 1 2.3 2.3S9.8 7.5 8.5 7.5 6.2 6.6 6.2 5.3 7.2 3 8.5 3zm-2 6h4v12h-4V9zm7 0h3.8v1.7h.1c.5-.9 1.8-1.9 3.7-1.9 4 0 4.7 2.6 4.7 6v6.2h-4v-5.5c0-1.3 0-3-1.8-3s-2.1 1.4-2.1 2.9V21h-4V9z" />
    ),
    youtube: (
      <path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18 5 12 5 12 5s-6 0-7.8.4a2.5 2.5 0 0 0-1.8 1.8C2 9 2 12 2 12s0 3 .4 4.8a2.5 2.5 0 0 0 1.8 1.8C6 19 12 19 12 19s6 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.8.4-4.8.4-4.8s0-3-.4-4.8zM10 15.5v-7l6 3.5-6 3.5z" />
    ),
    instagram: (
      <path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm10 2H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm-5 3.5A4.5 4.5 0 1 1 7.5 13 4.5 4.5 0 0 1 12 8.5zm0 2A2.5 2.5 0 1 0 14.5 13 2.5 2.5 0 0 0 12 10.5zM17 6.8a1 1 0 1 1-1 1 1 1 0 0 1 1-1z" />
    ),
    x: (
      <path d="m4 4 6.6 8.8L4.3 20h2.2l5-5.4L15 20h5.7l-6.9-9.2L19 4h-2.2l-4.6 5L9.3 4H4z" />
    ),
  };

  return (
    <svg
      viewBox="0 0 24 24"
      fill="currentColor"
      className={className}
      aria-hidden="true"
    >
      {icons[name]}
    </svg>
  );
}
