import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'

Vue.use(Router)

const Home = () => import('~/pages/home').then(m => m.default || m)
const Welcome = () => import('~/pages/index').then(m => m.default || m)

const Login = () => import('~/pages/auth/login').then(m => m.default || m)
const Register = () => import('~/pages/auth/register').then(m => m.default || m)
const PasswordReset = () => import('~/pages/auth/password/reset').then(m => m.default || m)
const PasswordRequest = () => import('~/pages/auth/password/email').then(m => m.default || m)

const Settings = () => import('~/pages/user/settings/settings').then(m => m.default || m)
const SettingsProfile = () => import('~/pages/user/settings/profile').then(m => m.default || m)
const SettingsPassword = () => import('~/pages/user/settings/password').then(m => m.default || m)

const MarkersIndex = () => import('~/pages/marker/index').then(m => m.default || m)
const ToursIndex = () => import('~/pages/tour/index').then(m => m.default || m)

const MarkerShow = () => import('~/pages/marker/_show').then(m => m.default || m)
const TourShow= () => import('~/pages/tour/_show').then(m => m.default || m)


const About = () => import('~/pages/aboutus').then(m => m.default || m)
const Contact = () => import('~/pages/contact').then(m => m.default || m)

// User cart
const UserShoppingCart = () => import('~/pages/user/cart/cart').then(m => m.default || m)
const CartWish = () => import('~/pages/user/cart/wish').then(m => m.default || m)
const CartProcessing = () => import('~/pages/user/cart/processing').then(m => m.default || m)
const CartChecked = () => import('~/pages/user/cart/checked').then(m => m.default || m)
const CartHistory = () => import('~/pages/user/cart/history').then(m => m.default || m)

const FavoriteSights = () => import('~/pages/user/favorites/favoriteSights').then(m => m.default || m)
const FavoriteTours = () => import('~/pages/user/favorites/favoriteTours').then(m => m.default || m)

// admin pages
const AdminIndex = () => import('~/pages/admin/index').then(m => m.default || m)
const AdminLogin = () => import('~/pages/admin/auth/login').then(m => m.default || m)
const AdminDash = () => import('~/pages/admin/dash').then(m => m.default || m)

const AdminMarker = () => import('~/pages/admin/marker/adminMarkerIndex').then(m => m.default || m)
const AdminMarkerList = () => import('~/pages/admin/marker/adminMarkerList').then(m => m.default || m)
const AdminMarkerCategory = () => import('~/pages/admin/marker/adminMarkerCategory').then(m => m.default || m)

const AdminTour = () => import('~/pages/admin/tour/adminTourIndex').then(m => m.default || m)
const AdminTourList = () => import('~/pages/admin/tour/adminTourList').then(m => m.default || m)
const AdminTourCategory = () => import('~/pages/admin/tour/adminTourCategory').then(m => m.default || m)

const AdminSettlements = () => import('~/pages/admin/settlement/SettlementsIndex').then(m => m.default || m)
const AdminSettlementsTrash = () => import('~/pages/admin/settlement/trashed').then(m => m.default || m)
const AdminSettlementsIndex = () => import('~/pages/admin/settlement/index').then(m => m.default || m)
//orders
const OrdersAdmin = () => import('~/pages/admin/tour/orders').then(m => m.default || m)

const NotificationsMessage = () => import('~/pages/admin/notifications/contactUsMessage').then(m => m.default || m)
// Users
const UsersAdmins = () => import('~/pages/admin/users/admins').then(m => m.default || m)
const UsersUsers = () => import('~/pages/admin/users/users').then(m => m.default || m)
const AdminProfile = () => import('~/pages/admin/users/profile').then(m => m.default || m)

const AdminPagesAbout = () => import('~/pages/admin/pages/about').then(m => m.default || m)


const routes = [

  { path: '/', name: 'welcome', component: Welcome },

    //
  { path: '/sights', name: 'sights', component: MarkersIndex },
  { path: '/sight/:slug', name: 'sight.show', component: MarkerShow },
  { path: '/tours', name: 'tours', component: ToursIndex },
  { path: '/tour/:slug', name: 'tour.show', component: TourShow },
  { path: '/about', name: 'about', component: About },
  { path: '/contact', name: 'contact', component: Contact },
    //
    //User
    { path: '/user', component: UserShoppingCart,
      children: [
        { path: '', redirect: { name: 'cart.cart' } },
        { path: 'cart/cart', name: 'cart.cart', component: CartWish },
        { path: 'cart/processing', name: 'cart.processing', component: CartProcessing },
        { path: 'cart/checked', name: 'cart.checked', component: CartChecked },
        { path: 'cart/history', name: 'cart.history', component: CartHistory },

        { path: 'favorite/tours', name: 'user.favorite.tours', component: FavoriteTours },
        { path: 'favorite/sights', name: 'user.favorite.sights', component: FavoriteSights },
      ] },
  // User login/register/reset
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/password/reset', name: 'password.request', component: PasswordRequest },
  { path: '/password/reset/:token', name: 'password.reset', component: PasswordReset },

    // User profile
  { path: '/settings',
    component: Settings,
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: SettingsProfile },
      { path: 'password', name: 'settings.password', component: SettingsPassword }
    ] },
    // {path:'/404', name: '404', component: { template: '<p>Page Not Found</p>'  }}



 // Admin pages routes
  { path: '/admin', name: 'admin.index', component: AdminIndex },
  { path: '/admin/login', name: 'admin.login', component: AdminLogin },
  { path: '/admin/dash', name: 'admin.dash', component: AdminDash },

  { path: '/admin/marker/:slug?', name: 'admin.marker', component: AdminMarker },
  { path: '/admin/markers', name: 'admin.marker.list', component: AdminMarkerList },
  { path: '/admin/category/marker', name: 'admin.marker.category', component: AdminMarkerCategory},

  { path: '/admin/tour/:slug?', name: 'admin.tour', component: AdminTour},
  { path: '/admin/tours', name: 'admin.tour.list', component: AdminTourList},
  { path: '/admin/category/tour', name: 'admin.tour.category', component: AdminTourCategory },

  { path: '/admin/settlement',
    component: AdminSettlements,
    children: [
      { path: '', redirect: {  name: 'admin.settlement' } },
      { path: 'index', name: 'admin.settlement', component: AdminSettlementsIndex },
      { path: 'trashed', name: 'ast', component: AdminSettlementsTrash }
    ]
  },

  { path: '/admin/notifications/message', name: 'admin.notifications.message', component: NotificationsMessage },
  { path: '/admin/orders', name: 'admin.orders', component: OrdersAdmin },
    // users
  { path: '/admin/users/users', name: 'admin.users', component: UsersUsers },
  { path: '/admin/users/admins', name: 'admin.admins', component: UsersAdmins },
  { path: '/admin/profile', name: 'admin.profile', component: AdminProfile },

  { path: '/admin/pages/about', name: 'admin.about', component: AdminPagesAbout },

]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
