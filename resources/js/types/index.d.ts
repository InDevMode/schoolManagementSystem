export interface User {
    id: number;
    name: string;
    last_name: string;
    email: string;
    user_type: number;
    profile_picture: string | null;
    status: number;
    roles: string[];
    permissions: string[];
}

export interface PageProps {
    [key: string]: unknown;
    auth: {
        user: User | null;
    };
    settings: {
        school_name: string;
        logo_url: string;
        kkiapay_public_key?: string;
        stripe_public_key?: string;
        fedapay_public_key?: string;
    } | null;
    flash: {
        success?: string;
        error?: string;
        warning?: string;
    };
    notifications: any[];
    unreadMessages: any[];
    ziggy: {
        location: string;
        [key: string]: unknown;
    };
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
}

export interface NavItem {
    id: string;
    label: string;
    icon: string;
    route?: string;
    href?: string;
    children?: NavItem[];
    permission?: string;
}

export interface SelectOption {
    value: string | number;
    label: string;
    disabled?: boolean;
}
