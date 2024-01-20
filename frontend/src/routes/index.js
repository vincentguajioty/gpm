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
import GererConsommation from 'pages/publicAccess/tracerConso/gererConsommation';

import Home from 'pages/internalApp/home/home';
import Login from 'pages/home/login';
import Logout from 'pages/home/logout';
import LoginCGU from 'pages/home/loginCGU';
import LoginPwdForgotten from 'pages/home/loginPwdForgotten';
import LoginPwdChange from '../pages/home/loginPwdChange';

import Lots from 'pages/internalApp/lots/lots';
import LotDetails from 'pages/internalApp/lots/lotsOpe/lotDetails';
import Sacs from 'pages/internalApp/lots/sacs';
import Materiels from 'pages/internalApp/lots/materiels';
import AlertesBenevolesLots from 'pages/internalApp/lots/alertesBenevolesLots';
import RapportsConso from 'pages/internalApp/lots/rapportsConso';
import LotInventaireEnCours from 'pages/internalApp/lots/inventairesLots/inventaireEnCours';

import Reserves from 'pages/internalApp/reserves/reserves';
import ConteneurDetails from 'pages/internalApp/reserves/conteneurs/conteneurDetails';
import MaterielsReserve from 'pages/internalApp/reserves/materielsReserve';
import ReserveInventaireEnCours from 'pages/internalApp/reserves/inventairesConteneurs/inventaireEnCours';

import Commandes from 'pages/internalApp/commandes/commandes';
import OneCommande from 'pages/internalApp/commandes/oneCommande/oneCommande';
import CentresDeCouts from 'pages/internalApp/centresDeCouts/centresDeCouts';
import OneCentre from 'pages/internalApp/centresDeCouts/oneCentre/oneCentre';
import Fournisseurs from 'pages/internalApp/fournisseurs/fournisseurs';
import FournisseurDetails from 'pages/internalApp/fournisseurs/fournisseurDetails';

import Frequences from 'pages/internalApp/transmissions/frequences';
import Plans from 'pages/internalApp/transmissions/plans';
import Equipements from 'pages/internalApp/transmissions/equipements';
import EquipementVhfDetails from 'pages/internalApp/transmissions/equipementDetails';

import Vehicules from 'pages/internalApp/vehicules/vehicules';
import VehiculeDetails from 'pages/internalApp/vehicules/vehiculeDetails';
import SuiviDesinfections from 'pages/internalApp/vehicules/suiviDesinfections';
import SuiviMaintenances from 'pages/internalApp/vehicules/suiviMaintenances';
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
        <Route path="/consoPublic/:idConsommation" element={<GererConsommation />} />
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
          <Route path="/lots/:idLot" element={<LotDetails />} />
          <Route path="/sacs" element={<Sacs />} />
          <Route path="/lotsMateriel" element={<Materiels />} />
          <Route path="/lotsAlertesBenevoles" element={<AlertesBenevolesLots />} />
          <Route path="/rapportsConso" element={<RapportsConso />} />
          <Route path="/inventaireLotEnCours/:idInventaire" element={<LotInventaireEnCours />} />

          <Route path="/reservesConteneurs" element={<Reserves />} />
          <Route path="/reservesConteneurs/:idConteneur" element={<ConteneurDetails />} />
          <Route path="/reservesMateriels" element={<MaterielsReserve />} />
          <Route path="/inventaireReserveEnCours/:idReserveInventaire" element={<ReserveInventaireEnCours />} />

          <Route path="/commandes" element={<Commandes />} />
          <Route path="/commandes/:idCommande" element={<OneCommande />} />
          <Route path="/couts" element={<CentresDeCouts />} />
          <Route path="/couts/:idCentreDeCout" element={<OneCentre />} />
          <Route path="/fournisseurs" element={<Fournisseurs />} />
          <Route path="/fournisseurs/:idFournisseur" element={<FournisseurDetails />} />

          <Route path="/vhfFrequences" element={<Frequences />} />
          <Route path="/vhfPlans" element={<Plans />} />
          <Route path="/vhfEquipements" element={<Equipements />} />
          <Route path="/vhfEquipements/:idVhfEquipement" element={<EquipementVhfDetails />} />

          <Route path="/vehicules" element={<Vehicules />} />
          <Route path="/vehicules/:idVehicule" element={<VehiculeDetails />} />
          <Route path="/vehiculesDesinfections" element={<SuiviDesinfections />} />
          <Route path="/vehiculesMaintenances" element={<SuiviMaintenances />} />
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
