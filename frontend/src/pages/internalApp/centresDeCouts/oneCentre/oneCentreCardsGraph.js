import React, { useEffect, useRef, useState } from 'react';
import ReactEChartsCore from 'echarts-for-react/lib/core';
import { PieChart } from 'echarts/charts';
import {
  GridComponent,
  LegendComponent,
  TitleComponent,
  TooltipComponent
} from 'echarts/components';
import * as echarts from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { getColor, rgbaColor } from 'helpers/utils';
import PropTypes from 'prop-types';

echarts.use([
  TitleComponent,
  TooltipComponent,
  GridComponent,
  PieChart,
  CanvasRenderer,
  LegendComponent
]);

const OneCentreCardsGraph = ({ data, soldeActuel, budgetTotalPositif }) => {
    const chartRef = useRef(null);

    const [otpionsForGraph, setOtpionsForGraph] = useState({});

    const initGraph = () => {
        let initialOptions = {
            tooltip: {
                trigger: 'item',
                backgroundColor: getColor('gray-100'),
                textStyle: { color: getColor('dark') },
            },
            legend: { show: false },
            series:[],
        }

        //Serie1 - details:
        let serieDetails = {
            name: 'Répartition détaillée',
            type: 'pie',
            radius: ['45%', '60%'],
            selectedMode: 'single',
            itemStyle: {
                borderWidth: 2,
                borderColor: getColor('gray-100')
            },
            label: { show: false },
            labelLine: { show: false },
            data: [],
        }
        for(const section of data)
        {
            for(const sousSection of section.sousSections)
            {
                serieDetails.data.push({
                    value: sousSection.montant,
                    name: sousSection.sousSectionName,
                    itemStyle: {
                        color: rgbaColor(getColor(sousSection.color), sousSection.colorOpacity/100),
                    }
                })
            }
        }
        initialOptions.series.push(serieDetails);

        //Serie2 - Global:
        let serieGlobal = {
            name: 'Répartition générale',
            type: 'pie',
            radius: ['70%', '75%'],
            barWidth: 10,
            itemStyle: {
                borderWidth: 2,
                borderColor: getColor('gray-100')
            },
            label: { show: false },
            labelLine: { show: false },
            data: [],
        }
        for(const section of data)
        {
            serieGlobal.data.push({
                value: section.montant,
                name: section.sectionName,
                itemStyle: { color: rgbaColor(getColor(section.color), section.colorOpacity/100) },
                label: {
                    rich: {
                        per: {
                            color: '#1C4F93'
                        }
                    }
                }
            });
        }
        initialOptions.series.push(serieGlobal);

        setOtpionsForGraph(initialOptions);
    }

    useEffect(()=>{
        initGraph();
    },[data, budgetTotalPositif])

    return (
        <div className="position-relative py-2">
            <ReactEChartsCore
                ref={chartRef}
                echarts={echarts}
                option={otpionsForGraph}
                style={{ minHeight: '18.75rem' }}
            />
            <div className="position-absolute top-50 start-50 translate-middle text-center">
                <div className="rounded-circle d-flex flex-center marketing-exp-circle">
                    <h4 className="mb-0 text-900">{soldeActuel} €</h4>
                </div>
            </div>
        </div>
    );
};

OneCentreCardsGraph.propTypes = {};

export default OneCentreCardsGraph;
