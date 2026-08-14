// services/api.ts

import axios from "axios";

const rawBaseUrl = import.meta.env.VITE_API_BASE_URL;
const baseURL = `${rawBaseUrl.replace(/\/+$/, "")}/api`;

const api = axios.create({
    baseURL,
    headers: {
        "Accept": "Application/json"
    },
});

api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("apiToken");
        if(token){
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if(error.response && error.response.status === 401){
            localStorage.removeItem("apiToken");
            localStorage.removeItem("user");

            if(window.location.pathname !== "/login"){
                window.location.href ="/login";
            }
        }

        return Promise.reject(error);
    }
);

export default api;