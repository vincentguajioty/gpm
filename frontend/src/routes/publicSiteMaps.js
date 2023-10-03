export const publicRoutes = {
    label: 'Accès libre',
    labelDisable: false,
    children: [
        {
            name: 'Accueil',
            to: '/',
            icon: 'home',
            exact: true,
            active: true
        },
        {
            name: 'Tracer une consommation',
            to: '/consoPublic',
            icon: 'notes-medical',
            exact: true,
            active: true
        },
        {
            name: 'Déclarer un incident',
            to: '/incidentPublic',
            icon: 'exclamation-triangle',
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
            icon: 'user',
            exact: true,
            active: true
        }
    ]
};
  
export default [
    publicRoutes,
    loginRoutes,
];
  