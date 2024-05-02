import React, {useState, useEffect} from 'react';
import ReactEChartsCore from 'echarts-for-react/lib/core';
import { Spinner } from 'react-bootstrap';
import { getColor } from 'helpers/utils';
import { LineChart } from 'echarts/charts';
import * as echarts from 'echarts/core';
import {
  GridComponent,
  TooltipComponent,
  TitleComponent,
  LegendComponent,
  MarkPointComponent,
  MarkLineComponent,
  DataZoomComponent
} from 'echarts/components';
import { CanvasRenderer } from 'echarts/renderers';
import { tooltipFormatter } from 'helpers/echart-utils';

echarts.use([
    TitleComponent,
    TooltipComponent,
    GridComponent,
    LineChart,
    CanvasRenderer,
    LegendComponent,
    MarkPointComponent,
    MarkLineComponent,
    DataZoomComponent
]);

const VehiculeRelevesKMGraph = ({relevesKM}) => {
    const [graphSpinner, setGraphSpinner] = useState(true);

    const [listeValues, setListeValues] = useState([]);

    const makeBoxReady = async () => {
        try {
            let tempArray = [];
            for(const item of relevesKM)
            {
                tempArray.push([new Date(item.dateReleve).valueOf(), item.releveKilometrique])
            }
            setListeValues(tempArray);

            setGraphSpinner(false);
        } catch (e) {
            console.log(e);
        }
    }

    useEffect(() => {
        makeBoxReady();
    }, [])

    useEffect(() => {
        makeBoxReady();
    }, [relevesKM])

    const getOption = () => ({
        color: [
          getColor('primary'),
        ],
        tooltip: {
            trigger: 'axis',
            padding: [7, 10],
            backgroundColor: getColor('100'),
            borderColor: getColor('300'),
            textStyle: { color: getColor('dark') },
            borderWidth: 1,
            transitionDuration: 0,
            axisPointer: {
                type: 'none'
            },
            formatter: tooltipFormatter
        },
        xAxis: {
          id: 0,
          type: 'time',
          boundaryGap: false,
          axisLine: {
            lineStyle: {
              color: getColor('300'),
              type: 'solid'
            }
          },
          axisTick: { show: false },
          axisLabel: {
              color: getColor('400'),
              margin: 15
          },
          axisPointer: {
            show: true
          },
          splitLine: {
            show: false
          }
        },
        axisPointer: {
          link: [
            {
              xAxisIndex: [0]
            }
          ]
        },
        yAxis: {
          type: 'value',
          scale: true,
          splitLine: {
            lineStyle: {
              color: getColor('200'),
            }
          },
          boundaryGap: false,
          axisLabel: {
            show: true,
            color: getColor('400'),
            margin: 15
          },
          axisTick: { show: false },
          axisLine: { show: false }
        },
        series: [
            {
                name: 'KM',
                type: 'line',
                data: listeValues,
                symbolSize: 10,
                itemStyle: {
                    color: getColor('white'),
                    borderColor: getColor('primary'),
                    borderWidth: 2
                },
                lineStyle: {
                    color: getColor('primary')
                },
                symbol: 'circle',
                smooth: true,
            },
        ],
        grid: [
          {
            right: '8%',
            left: '5%',
            bottom: 25,
            height: 230,
            containLabel: true
          },
        ],
        dataZoom: [
          {
            id: 'dataZoomX',
            type: 'slider',
            xAxisIndex: [0] ,
            filterMode: 'filter',
            handleIcon:
              'path://M10.7,11.9H9.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4h1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
            handleSize: '120%',
            bottom: 9,
            height: 16,
          },
          {
            type: 'inside',
            xAxisIndex: [0],
          }
        ],
    });

    return(
        graphSpinner ? (
            <center><Spinner animation="border" role="status">
                <span className="visually-hidden">Chargement en cours ...</span>
            </Spinner></center>
            ) : (
            <ReactEChartsCore
                className='mb-3'    
                echarts={echarts}
                option={getOption()}
                style={{ height: '18.75rem' }}
            />
            )
    );

}

VehiculeRelevesKMGraph.propTypes = {
};
  
export default VehiculeRelevesKMGraph;