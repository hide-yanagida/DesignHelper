export default {
    methods: {
        // isLogin
        setToken(token){
            axios.defaults.headers.common['Authorization'] = token;
            localStorage.setItem('dhApiToken', token);
            state.isLogin = true;
        },
        getToken() {
            const token = localStorage.getItem('dhApiToken');
            if (token) this.setToken(token);
        },
        removeToken() {
            axios.defaults.headers.common['Authorization'] = '';
            state.isLogin = false;
            localStorage.removeItem("dhApiToken");
        },

        // cart
        addCart(item){
            state.cart.push(item);
        },

        // user
        setUser() {
            axios.get('/api/me').then(res => {
                state.user.id = res.data.id;
                state.user.name = res.data.name;
                state.user.email = res.data.email;
                state.user.role = res.data.role;
            }).catch(error => {
                this.isError = true;
            });
        },
        removeUser() {
            state.user.id = "";
            state.user.name = "";
            state.user.email = "";
            state.user.role = "";
        }
    }
}