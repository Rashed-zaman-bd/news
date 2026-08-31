// src/types/opinion.ts

export interface Opinion {
    id: number;
    title: string;
    slug: string;
    writer_name: string;
    writer_designation: string | null;
    writer_image: string | null;
    text: string;
    image: string | null;
    is_published: boolean;
    published_at: string | null;
    sort_order: number;
    created_at: string;
    updated_at: string;
}