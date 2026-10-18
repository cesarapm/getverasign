import { ref, computed, watch } from "vue";
import { useRouter } from "vue-router";
import http from "../utils/http";

// Estados globales
const user = ref(null);
const token = ref(localStorage.getItem("auth_token"));
const isAuthenticated = computed(() => !!token.value && !!user.value);
const isLoading = ref(false);
const error = ref("");

/**
 * Composable para manejar autenticación
 */
export function useAuth() {
    const router = useRouter();

    /**
     * Inicializar autenticación (cargar usuario al montar la app)
     */
    const initAuth = async () => {
        const storedToken = localStorage.getItem("auth_token");
        const storedUser = localStorage.getItem("auth_user");

        if (storedToken) {
            token.value = storedToken;

            if (storedUser) {
                user.value = JSON.parse(storedUser);
            } else {
                // Verificar si el token sigue siendo válido
                await fetchProfile();
            }
        }
    };

    /**
     * Obtener perfil del usuario autenticado
     */
    const fetchProfile = async () => {
        if (!token.value) return;

        try {
            isLoading.value = true;
            error.value = "";

            const response = await http.get("/profile");

            if (response.data.success) {
                user.value = response.data.user;
                localStorage.setItem("auth_user", JSON.stringify(user.value));
            }
        } catch (err) {
            console.error("Error al obtener perfil:", err);
            logout();
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Iniciar sesión
     */
    const login = async (email, password) => {
        try {

            isLoading.value = true;
            error.value = "";

            const response = await http.post("/login", {
                email,
                password,
            });

            if (response.data.success) {
                token.value = response.data.token;
                user.value = response.data.user;

                // Guardar en localStorage
                localStorage.setItem("auth_token", token.value);
                localStorage.setItem("auth_user", JSON.stringify(user.value));

                // Redirigir al dashboard
                await router.push({ name: "Dashboard" });

                return { success: true };
            }
        } catch (err) {
            const message =
                err.response?.data?.message ||
                err.response?.data?.errors?.email?.[0] ||
                "Error al iniciar sesión.";
            error.value = message;
            return { success: false, error: message };
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Registrar nuevo usuario
     */
    const register = async (name, email, password, passwordConfirmation) => {
        try {
            isLoading.value = true;
            error.value = "";

            const response = await http.post("/register", {
                name,
                email,
                password,
                password_confirmation: passwordConfirmation,
            });

            if (response.data.success) {
                token.value = response.data.token;
                user.value = response.data.user;

                // Guardar en localStorage
                localStorage.setItem("auth_token", token.value);
                localStorage.setItem("auth_user", JSON.stringify(user.value));

                // Redirigir al dashboard
                await router.push({ name: "Dashboard" });

                return { success: true };
            }
        } catch (err) {
            // Obtener primer error de validación
            const errors = err.response?.data?.errors || {};
            const firstError =
                Object.values(errors)[0]?.[0] ||
                err.response?.data?.message ||
                "Error al registrarse.";
            error.value = firstError;
            return { success: false, error: firstError };
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Cerrar sesión
     */
    const logout = async () => {
        try {
            if (token.value) {
                await http.post("/logout");
            }
        } catch (err) {
            console.error("Error al cerrar sesión:", err);
        } finally {
            // Limpiar estado local
            token.value = null;
            user.value = null;
            error.value = "";

            localStorage.removeItem("auth_token");
            localStorage.removeItem("auth_user");

            // Redirigir a login
            await router.push({ name: "Login" });
        }
    };

    return {
        // Estado
        user: computed(() => user.value),
        token: computed(() => token.value),
        isAuthenticated,
        isLoading: computed(() => isLoading.value),
        error: computed(() => error.value),

        // Métodos
        initAuth,
        fetchProfile,
        login,
        register,
        logout,
    };
}
