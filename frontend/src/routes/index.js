import React from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';
import AuthSimpleLayout from '../layouts/AuthSimpleLayout';
import PublicLayout from '../layouts/PublicLayout';
import MainLayout from '../layouts/MainLayout';
import ErrorLayout from '../layouts/ErrorLayout';

import ProtectedRoutes from '../components/connexion/protectedRoutes';

import Error404 from 'pages/errors/Error404';
import Error500 from 'pages/errors/Error500';

import Landing from 'pages/publicAccess/landing';
import DeclarationIncident from 'pages/publicAccess/declarationIncident';
import TracerConso from 'pages/publicAccess/tracerConso';

import Home from 'pages/internalApp/home/home';
import Login from 'pages/home/login';
import Logout from 'pages/home/logout';
import LoginCGU from 'pages/home/loginCGU';
import LoginPwdForgotten from 'pages/home/loginPwdForgotten';
import LoginPwdChange from '../pages/home/loginPwdChange';

import Lots from 'pages/internalApp/lots/lots';
import Sacs from 'pages/internalApp/lots/sacs';
import Emplacements from 'pages/internalApp/lots/emplacements';
import Materiels from 'pages/internalApp/lots/materiels';
import AlertesBenevolesLots from 'pages/internalApp/lots/alertesBenevolesLots';
import RapportsConso from 'pages/internalApp/lots/rapportsConso';

import Reserves from 'pages/internalApp/reserves/reserves';
import MaterielsReserve from 'pages/internalApp/reserves/materielsReserve';

import Commandes from 'pages/internalApp/commandes/commandes';
import CentresDeCouts from 'pages/internalApp/centresDeCouts/centresDeCouts';
import Fournisseurs from 'pages/internalApp/commandes/fournisseurs';
import FournisseurDetails from 'pages/internalApp/commandes/fournisseurDetails';

import Frequences from 'pages/internalApp/transmissions/frequences';
import Plans from 'pages/internalApp/transmissions/plans';
import Equipements from 'pages/internalApp/transmissions/equipements';

import Vehicules from 'pages/internalApp/vehicules/vehicules';
import SuiviDesinfections from 'pages/internalApp/vehicules/suiviDesinfections';
import SuiviMaintenances from 'pages/internalApp/vehicules/suiviMaintenances';
import GraphiquesKilometriques from 'pages/internalApp/vehicules/graphiquesKilometriques';
import AlertesBenevolesVehicules from 'pages/internalApp/vehicules/alertesBenevolesVehicules';

import Tenues from 'pages/internalApp/tenues/tenues';
import AffectationsTenues from 'pages/internalApp/tenues/affectations';
import Cautions from 'pages/internalApp/tenues/cautions';

import ReferentielsSettings from 'pages/internalApp/settings/referentiels/referentiels';
import ReferentielDetails from 'pages/internalApp/settings/referentiels/referentielDetails';
import LotsReservesSettings from 'pages/internalApp/settings/lotsReserves/lotsReserves';
import LieuxSettings from 'pages/internalApp/settings/lieux/lieux';
import VehiculesSettings from 'pages/internalApp/settings/vehicules/vehicules';
import TransmissionsSettings from 'pages/internalApp/settings/transmissions/transmissions';

import Utilisateurs from 'pages/internalApp/settings/utilisateurs/utilisateurs';
import UtilisateurDetails from 'pages/internalApp/settings/utilisateurs/utilisateurDetails';
import Profils from 'pages/internalApp/settings/profils/profils';
import Moncompte from 'pages/internalApp/settings/utilisateurs/monCompte';
import MessagesGeneraux from 'pages/internalApp/settings/messagesGeneraux/messagesGeneraux';
import EnvoiMailEquipe from 'pages/internalApp/settings/envoiMailEquipe/envoiMailEquipe';
import ToDoList from 'pages/internalApp/settings/todolist/todolist';

import ConfigGenerale from 'pages/internalApp/settings/configGenerale/configGenerale';
import ActionsMassives from 'pages/internalApp/settings/actionsMassives/actionsMassives';
import ManuelAide from 'pages/internalApp/settings/manuelAide/manuelAide';
import APropos from 'pages/internalApp/settings/aPropos/aPropos';

const FalconRoutes = () => {
  return (
    <Routes>
      <Route element={<AuthSimpleLayout />}>
        <Route path="/logout" element={<Logout />} />
      </Route>
      
      <Route element={<PublicLayout />}>
        <Route path="/" element={<Landing />} />
        <Route path="/incidentPublic" element={<DeclarationIncident />} />
        <Route path="/consoPublic" element={<TracerConso />} />
      </Route>

      <Route element={<AuthSimpleLayout />}>
        <Route path="/login" element={<Login />} />
      </Route>

      <Route path="/mdpOublie" element={<LoginPwdForgotten />} />
      <Route path="/mdpOublie/:token" element={<LoginPwdForgotten />} />
      

      <Route element={<ProtectedRoutes />}>
        <Route path="/cguAtLogin" element={<LoginCGU />} />
        <Route path="/changePwdAtLogin" element={<LoginPwdChange />} />

        <Route element={<MainLayout />}>
          <Route path="/home" element={<Home />} />

          <Route path="/lots" element={<Lots />} />
          <Route path="/sacs" element={<Sacs />} />
          <Route path="/emplacements" element={<Emplacements />} />
          <Route path="/lotsMateriel" element={<Materiels />} />
          <Route path="/lotsAlertesBenevoles" element={<AlertesBenevolesLots />} />
          <Route path="/rapportsConso" element={<RapportsConso />} />

          <Route path="/reservesConteneurs" element={<Reserves />} />
          <Route path="/reservesMateriels" element={<MaterielsReserve />} />

          <Route path="/commandes" element={<Commandes />} />
          <Route path="/couts" element={<CentresDeCouts />} />
          <Route path="/fournisseurs" element={<Fournisseurs />} />
          <Route path="/fournisseurs/:idFournisseur" element={<FournisseurDetails />} />

          <Route path="/vhfFrequences" element={<Frequences />} />
          <Route path="/vhfPlans" element={<Plans />} />
          <Route path="/vhfEquipements" element={<Equipements />} />

          <Route path="/vehicules" element={<Vehicules />} />
          <Route path="/vehiculesDesinfections" element={<SuiviDesinfections />} />
          <Route path="/vehiculesMaintenances" element={<SuiviMaintenances />} />
          <Route path="/vehiculesKilometres" element={<GraphiquesKilometriques />} />
          <Route path="/vehiculesAlertesBenevoles" element={<AlertesBenevolesVehicules />} />

          <Route path="/tenuesCatalogue" element={<Tenues />} />
          <Route path="/tenuesAffectation" element={<AffectationsTenues />} />
          <Route path="/tenuesCautions" element={<Cautions />} />

          <Route path="/settingsReferentiels" element={<ReferentielsSettings />} />
          <Route path="/settingsReferentiels/:idTypeLot" element={<ReferentielDetails />} />
          <Route path="/settingsLotsReserves" element={<LotsReservesSettings />} />
          <Route path="/settingsLieux" element={<LieuxSettings />} />
          <Route path="/settingsVehicules" element={<VehiculesSettings />} />
          <Route path="/settingsTransmissions" element={<TransmissionsSettings />} />

          <Route path="/teamUtilisateurs" element={<Utilisateurs />} />
          <Route path="/teamUtilisateurs/:idPersonne" element={<UtilisateurDetails />} />
          <Route path="/teamProfils" element={<Profils />} />
          <Route path="/monCompte" element={<Moncompte />} />
          <Route path="/teamMessages" element={<MessagesGeneraux />} />
          <Route path="/teamMail" element={<EnvoiMailEquipe />} />
          <Route path="/teamToDoList" element={<ToDoList />} />

          <Route path="/settingsGeneraux" element={<ConfigGenerale />} />
          <Route path="/settingsActionsMassives" element={<ActionsMassives />} />
          <Route path="/settingsAide" element={<ManuelAide />} />
          <Route path="/settingsFAQ" element={<APropos />} />

        </Route>
      </Route>

      <Route element={<ErrorLayout />}>
        <Route path="errors/404" element={<Error404 />} />
        <Route path="errors/500" element={<Error500 />} />
      </Route>

      {/* <Navigate to="/errors/404" /> */}
      <Route path="*" element={<Navigate to="/errors/404" replace />} />
    </Routes>
  );
};

export default FalconRoutes;
