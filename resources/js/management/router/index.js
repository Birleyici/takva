import { createRouter, createWebHistory } from 'vue-router';
import DashboardOverview from '../pages/dashboard/DashboardOverview.vue';
import CategoryList from '../pages/categories/CategoryList.vue';
import AuthorList from '../pages/authors/AuthorList.vue';
import ArticleList from '../pages/articles/ArticleList.vue';
import ArticleCreate from '../pages/articles/ArticleCreate.vue';
import ArticleEdit from '../pages/articles/ArticleEdit.vue';
import IssueList from '../pages/issues/IssueList.vue';
import IssueCreate from '../pages/issues/IssueCreate.vue';
import IssueEdit from '../pages/issues/IssueEdit.vue';
import VideoList from '../pages/videos/VideoList.vue';
import VideoCreate from '../pages/videos/VideoCreate.vue';
import VideoEdit from '../pages/videos/VideoEdit.vue';
import VideoCategoryList from '../pages/video-categories/VideoCategoryList.vue';
import HeroSlideList from '../pages/hero-slides/HeroSlideList.vue';
import MenuPageList from '../pages/menu-pages/MenuPageList.vue';
import MenuPageCreate from '../pages/menu-pages/MenuPageCreate.vue';
import MenuPageEdit from '../pages/menu-pages/MenuPageEdit.vue';
import SiteSettings from '../pages/settings/SiteSettings.vue';
import ThemeSettings from '../pages/settings/ThemeSettings.vue';
import AccountSettings from '../pages/settings/AccountSettings.vue';
import ContactMessageList from '../pages/contact/ContactMessageList.vue';

const routes = [
    {
        path: '/',
        name: 'management.dashboard',
        component: DashboardOverview,
        meta: {
            title: 'Kontrol Paneli',
            breadcrumb: 'Kontrol Paneli',
        },
    },
    {
        path: '/categories',
        name: 'management.categories',
        component: CategoryList,
        meta: {
            title: 'Kategoriler',
            breadcrumb: 'Kategoriler',
        },
    },
    {
        path: '/authors',
        name: 'management.authors',
        component: AuthorList,
        meta: {
            title: 'Yazarlar',
            breadcrumb: 'Yazarlar',
        },
    },
    {
        path: '/articles',
        name: 'management.articles',
        component: ArticleList,
        meta: {
            title: 'Makaleler',
            breadcrumb: 'Makaleler',
        },
    },
    {
        path: '/articles/create',
        name: 'management.articles.create',
        component: ArticleCreate,
        meta: {
            title: 'Makale Oluştur',
            breadcrumb: 'Makale Oluştur',
        },
    },
    {
        path: '/articles/:article/edit',
        name: 'management.articles.edit',
        component: ArticleEdit,
        meta: {
            title: 'Makale Düzenle',
            breadcrumb: 'Makale Düzenle',
        },
    },
    {
        path: '/issues',
        name: 'management.issues',
        component: IssueList,
        meta: {
            title: 'Sayılar',
            breadcrumb: 'Sayılar',
        },
    },
    {
        path: '/issues/create',
        name: 'management.issues.create',
        component: IssueCreate,
        meta: {
            title: 'Sayı Oluştur',
            breadcrumb: 'Sayı Oluştur',
        },
    },
    {
        path: '/issues/:issue/edit',
        name: 'management.issues.edit',
        component: IssueEdit,
        meta: {
            title: 'Sayı Düzenle',
            breadcrumb: 'Sayı Düzenle',
        },
    },
    {
        path: '/videos',
        name: 'management.videos',
        component: VideoList,
        meta: {
            title: 'Videolar',
            breadcrumb: 'Videolar',
        },
    },
    {
        path: '/videos/create',
        name: 'management.videos.create',
        component: VideoCreate,
        meta: {
            title: 'Video Oluştur',
            breadcrumb: 'Video Oluştur',
        },
    },
    {
        path: '/videos/:video/edit',
        name: 'management.videos.edit',
        component: VideoEdit,
        meta: {
            title: 'Video Düzenle',
            breadcrumb: 'Video Düzenle',
        },
    },
    {
        path: '/video-categories',
        name: 'management.video-categories',
        component: VideoCategoryList,
        meta: {
            title: 'Video Kategorileri',
            breadcrumb: 'Video Kategorileri',
        },
    },
    {
        path: '/hero-slides',
        name: 'management.hero-slides',
        component: HeroSlideList,
        meta: {
            title: 'Hero Slider',
            breadcrumb: 'Hero Slider',
        },
    },
    {
        path: '/menu-pages',
        name: 'management.menu-pages',
        component: MenuPageList,
        meta: {
            title: 'Menü Sayfaları',
            breadcrumb: 'Menü Sayfaları',
        },
    },
    {
        path: '/menu-pages/create',
        name: 'management.menu-pages.create',
        component: MenuPageCreate,
        meta: {
            title: 'Menü Sayfası Oluştur',
            breadcrumb: 'Menü Sayfası Oluştur',
        },
    },
    {
        path: '/menu-pages/:page/edit',
        name: 'management.menu-pages.edit',
        component: MenuPageEdit,
        meta: {
            title: 'Menü Sayfası Düzenle',
            breadcrumb: 'Menü Sayfası Düzenle',
        },
    },
    {
        path: '/contact-messages',
        name: 'management.contact-messages',
        component: ContactMessageList,
        meta: {
            title: 'İletişim Mesajları',
            breadcrumb: 'İletişim Mesajları',
        },
    },
    {
        path: '/settings/site',
        name: 'management.settings.site',
        component: SiteSettings,
        meta: {
            title: 'Site Ayarları',
            breadcrumb: 'Site Ayarları',
        },
    },
    {
        path: '/settings/theme',
        name: 'management.settings.theme',
        component: ThemeSettings,
        meta: {
            title: 'Tema Ayarları',
            breadcrumb: 'Tema Ayarları',
        },
    },
    {
        path: '/settings/account',
        name: 'management.settings.account',
        component: AccountSettings,
        meta: {
            title: 'Hesap Ayarları',
            breadcrumb: 'Hesap Ayarları',
        },
    },
];

const router = createRouter({
    history: createWebHistory('/management'),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});

export default router;
