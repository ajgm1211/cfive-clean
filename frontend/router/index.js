import Vue from 'vue'
import Router from 'vue-router'
import routes from './path'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  routes,
  scrollBehavior() {
    return { x: 0, y: 0 }
  }
})

/**
 * Protegemos las rutas para que solo puedan acceder
 * si el usuario esta autentificado, de lo contrario
 * lo redirigimos a la ventana de Login.
 * Si la ruta de la alcaldia es diferente a la del Login
 * lo redirigimos al login nuevamente.
 */

export default router
