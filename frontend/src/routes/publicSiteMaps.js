export const publicRoutes = {
    label: 'Accès libre',
    labelDisable: false,
    children: [
        {
            name: 'Accueil',
            to: '/',
            exact: true,
            active: true
        },
        {
            name: 'Tracer une consommation',
            to: '/',
            exact: true,
            active: true
        },
        {
            name: 'Déclarer un incident',
            to: '/',
            exact: true,
            active: true
        },
    ]
};

export const loginRoutes = {
    label: 'Equipe Logistique',
    labelDisable: false,
    children: [
        {
            name: 'Se connecter',
            to: '/login',
            exact: true,
            active: true
        }
    ]
};
  
export default [
    publicRoutes,
    loginRoutes,
];
  