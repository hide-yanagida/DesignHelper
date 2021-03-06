import * as types from './mutation-types'

export const state = {
    token: window.localStorage.getItem('token') || '',
    // refreshToken: window.localStorage.getItem('refreshToken') || '',
    user: window.localStorage.getItem('user') != {} ? JSON.parse(window.localStorage.getItem('user')) : {},
    emailToken: '',
}

export const mutations = {
    [types.LOGGED_IN] (state, payload) {
        state.token = payload.token
        // state.refreshToken = payload.refreshToken
    },
    [types.SET_USER] (state, payload) {
        state.user = payload.user
    },
    [types.LOGGED_OUT] (state) {
        state.token = ''
        // state.refreshToken = null
        state.user = {}
    },
    [types.SET_EMAIL_TOKEN] (state, payload) {
        state.emailToken = payload.emailToken
    },

}