export interface AdminCategory {
    id: number
    name: string
    slug: string
    icon: string | null
    parent_id: number | null
    parent_name?: string | null
    order: number
    is_active: boolean
    children: AdminCategory[]
    created_at?: string
}